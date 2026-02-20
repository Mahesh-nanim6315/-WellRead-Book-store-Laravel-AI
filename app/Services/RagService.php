<?php

namespace App\Services;

use App\Models\Book;
use App\Services\Contracts\LLMServiceInterface;
use Illuminate\Support\Facades\App;
use Throwable;

class RagService
{
    protected BookVectorSearchService $vectorSearch;

    public function __construct(BookVectorSearchService $vectorSearch)
    {
        $this->vectorSearch = $vectorSearch;
    }

    public function chat(
        string $userMessage,
        array $history = [],
        string $provider = 'openai'
    ): array {
        $userMessage = trim($userMessage);

        if ($userMessage === '') {
            return [
                'answer' => 'Type a message first.',
                'books' => [],
            ];
        }

        // Resolve selected LLM
        $llm = $this->resolveModel($provider);

        // Greeting detection (more natural)
        $normalized = strtolower($userMessage);
        $greetings = ['hi', 'hello', 'hey', 'good morning', 'good evening'];

        foreach ($greetings as $greet) {
            if (str_contains($normalized, $greet)) {
                return [
                    'answer' => "Hello! I can help you find books. Try: 'Suggest me a romantic novel'.",
                    'books' => [],
                ];
            }
        }

        // Generate embedding. If provider embedding API fails, fall back to keyword search.
        try {
            $queryEmbedding = $llm->embedding($userMessage);
        } catch (Throwable $e) {
            $topBooks = $this->keywordSearch($userMessage, 3);

            if (empty($topBooks)) {
                return [
                    'answer' => "The {$provider} embedding API is unavailable right now, and no keyword matches were found.",
                    'books' => [],
                ];
            }

            return [
                'answer' => $this->buildKeywordFallbackAnswer($topBooks, $provider),
                'books' => $topBooks,
            ];
        }

        if (empty($queryEmbedding) || count($queryEmbedding) < 10) {
            $topBooks = $this->keywordSearch($userMessage, 3);

            if (! empty($topBooks)) {
                return [
                    'answer' => $this->buildKeywordFallbackAnswer($topBooks, $provider),
                    'books' => $topBooks,
                ];
            }

            return [
                'answer' => 'I could not understand that query. Try a more specific request.',
                'books' => [],
            ];
        }

        // Vector search:
        // 1) prefer same-provider embeddings
        // 2) fall back to any embeddings (legacy rows may not have embedding_provider)
        // 3) relax threshold slightly for short/vague phrasing
        $topBooks = $this->vectorSearch->search($queryEmbedding, 3, 0.35, $provider);

        if (empty($topBooks)) {
            $topBooks = $this->vectorSearch->search($queryEmbedding, 3, 0.35, null);
        }

        if (empty($topBooks)) {
            $topBooks = $this->vectorSearch->search($queryEmbedding, 3, 0.20, null);
        }

        if (empty($topBooks)) {
            return [
                'answer' => "No relevant books found for provider '{$provider}'. Rebuild embeddings for this provider using: php artisan books:generate-embeddings --provider={$provider} --force",
                'books' => [],
            ];
        }

        $context = $this->buildBookContext($topBooks);
        $conversation = $this->buildConversationContext($history);

        $prompt = <<<PROMPT
You are a helpful AI book assistant for an online bookstore.

Conversation so far:
{$conversation}

Current user question:
{$userMessage}

Relevant books from the catalog:
{$context}

IMPORTANT:
- Only use books listed in "Relevant books from the catalog".
- Do NOT invent books.
- Recommend up to 3 books only.
- Explain briefly why each recommendation matches.
- Keep the tone concise and friendly.
- If user follow-up depends on earlier context, use conversation history.
PROMPT;

        try {
            $answer = $llm->generate($prompt);
        } catch (Throwable $e) {
            // Provider may be down/rate-limited. Still return useful retrieval output.
            $answer = $this->buildFallbackAnswerFromBooks($topBooks, $provider);
        }

        return [
            'answer' => $answer,
            'books' => $topBooks,
        ];
    }

    /**
     * Resolve LLM provider dynamically via config.
     */
    private function resolveModel(string $provider): LLMServiceInterface
    {
        $class = config("ai.providers.$provider");

        if (! $class || ! class_exists($class)) {
            $class = config("ai.providers.openai");
        }

        return App::make($class);
    }

    private function buildBookContext(array $topBooks): string
    {
        $rows = [];

        foreach ($topBooks as $item) {
            $book = $item['book'];

            $rows[] = sprintf(
                "- Title: %s | Author: %s | Category: %s | Genre: %s | Description: %s | Score: %.4f",
                $book->name,
                $book->author->name ?? 'Unknown',
                $book->category->name ?? 'Unknown',
                $book->genre->name ?? 'Unknown',
                trim((string) $book->description),
                (float) $item['score']
            );
        }

        return implode("\n", $rows);
    }

    private function buildConversationContext(array $history, int $maxTurns = 6): string
    {
        if (empty($history)) {
            return 'No prior conversation.';
        }

        $recent = array_slice($history, -$maxTurns * 2);
        $lines = [];

        foreach ($recent as $turn) {
            $role = $turn['role'] ?? 'user';
            $text = trim((string) ($turn['text'] ?? ''));

            if ($text === '') {
                continue;
            }

            $lines[] = strtoupper($role) . ': ' . $text;
        }

        return empty($lines)
            ? 'No prior conversation.'
            : implode("\n", $lines);
    }

    private function buildFallbackAnswerFromBooks(array $topBooks, string $provider): string
    {
        $lines = [
            "I found relevant books, but the {$provider} text API is temporarily unavailable or rate-limited.",
            'Here are top matches from your catalog:',
        ];

        foreach (array_slice($topBooks, 0, 3) as $item) {
            $book = $item['book'];
            $lines[] = sprintf(
                '- %s by %s (%s, %s)',
                $book->name,
                $book->author->name ?? 'Unknown',
                $book->category->name ?? 'Unknown',
                $book->genre->name ?? 'Unknown'
            );
        }

        return implode("\n", $lines);
    }

    private function buildKeywordFallbackAnswer(array $topBooks, string $provider): string
    {
        $lines = [
            "The {$provider} AI service is unavailable, so I matched books using catalog keywords.",
            'Top matches:',
        ];

        foreach (array_slice($topBooks, 0, 3) as $item) {
            $book = $item['book'];
            $lines[] = sprintf(
                '- %s by %s (%s, %s)',
                $book->name,
                $book->author->name ?? 'Unknown',
                $book->category->name ?? 'Unknown',
                $book->genre->name ?? 'Unknown'
            );
        }

        return implode("\n", $lines);
    }

    private function keywordSearch(string $message, int $limit = 3): array
    {
        $terms = $this->extractKeywords($message);

        if (empty($terms)) {
            return [];
        }

        $query = Book::with(['author', 'category', 'genre'])
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%' . $term . '%';

                    $q->orWhere('name', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', $like))
                        ->orWhereHas('genre', fn ($gq) => $gq->where('name', 'like', $like))
                        ->orWhereHas('author', fn ($aq) => $aq->where('name', 'like', $like));
                }
            })
            ->limit($limit)
            ->get();

        $rows = [];

        foreach ($query as $book) {
            $rows[] = [
                'book' => $book,
                'score' => 0.0,
            ];
        }

        return $rows;
    }

    private function extractKeywords(string $message): array
    {
        $normalized = strtolower($message);
        $tokens = preg_split('/[^a-z0-9]+/', $normalized) ?: [];
        $stopWords = [
            'the', 'a', 'an', 'and', 'or', 'of', 'for', 'to', 'in', 'on', 'with',
            'by', 'me', 'my', 'i', 'give', 'show', 'suggest', 'recommend', 'books', 'book',
        ];

        $terms = [];
        foreach ($tokens as $token) {
            if ($token === '' || strlen($token) < 3) {
                continue;
            }
            if (in_array($token, $stopWords, true)) {
                continue;
            }
            $terms[] = $token;
        }

        return array_values(array_unique($terms));
    }
}
