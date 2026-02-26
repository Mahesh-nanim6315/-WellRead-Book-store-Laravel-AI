<?php

namespace App\Services;

use App\Services\Agent\AgentPlannerExecutor;
use App\Services\Agent\AnswerComposer;
use App\Services\Agent\BookRetriever;
use App\Services\Agent\QueryIntentParser;
use App\Services\Contracts\LLMServiceInterface;
use Illuminate\Support\Facades\App;

class RagService
{
    public function __construct(
        private readonly QueryIntentParser $parser,
        private readonly BookRetriever $retriever,
        private readonly AgentPlannerExecutor $planner,
        private readonly AnswerComposer $composer,
    ) {
    }

    public function chat(
        string $userMessage,
        array $history = [],
        string $provider = 'ollama'
    ): array {
        $userMessage = trim($userMessage);
        if ($userMessage === '') {
            return [
                'answer' => 'Type a message first.',
                'books' => [],
                'source' => 'local_validation',
            ];
        }

        if ($this->parser->isGreeting($userMessage)) {
            return [
                'answer' => "Hello! I can help you find books. Try: 'Suggest me a romantic novel'.",
                'books' => [],
                'source' => 'local_intent',
            ];
        }

        $factAnswer = $this->tryAuthorFactAnswer($userMessage);
        if ($factAnswer !== null) {
            return $factAnswer;
        }

        $priceAnswer = $this->tryBookPriceAnswer($userMessage);
        if ($priceAnswer !== null) {
            return $priceAnswer;
        }

        $authorBooksIntent = $this->parser->parseAuthorBooksIntent($userMessage);
        if ($authorBooksIntent !== null) {
            $rows = $this->retriever->findBooksByAuthor(
                (string) $authorBooksIntent['author'],
                (int) $authorBooksIntent['limit']
            );

            if (empty($rows)) {
                return [
                    'answer' => "I couldn't find books by " . $authorBooksIntent['author'] . ' in the catalog.',
                    'books' => [],
                    'source' => 'catalog_rule',
                ];
            }

            return [
                'answer' => $this->composer->buildAgentFallbackAnswer($rows, []),
                'books' => array_slice($rows, 0, (int) $authorBooksIntent['limit']),
                'source' => 'catalog_rule',
            ];
        }

        $listIntent = $this->parser->parseListIntent($userMessage);
        if ($listIntent !== null) {
            $constraints = $this->parser->parseHardConstraints($userMessage);
            $rows = $this->retriever->retrieveByTopic(
                (string) $listIntent['topic'],
                (int) $listIntent['limit']
            );
            $rows = $this->retriever->enforceRowsAgainstConstraints($rows, $constraints);

            if (empty($rows)) {
                return [
                    'answer' => $this->composer->buildNoConstraintMatchAnswer($constraints),
                    'books' => [],
                    'source' => 'catalog_constraints',
                ];
            }

            return [
                'answer' => $this->composer->buildAgentFallbackAnswer($rows, []),
                'books' => array_slice($rows, 0, (int) $listIntent['limit']),
                'source' => 'catalog_rule',
            ];
        }

        $llm = $this->resolveModel($provider);
        $embeddingProvider = config('ai.embedding_provider', 'ollama');
        $embeddingLlm = $this->resolveModel($embeddingProvider);

        $conversation = $this->composer->buildConversationContext($history);
        $constraints = $this->parser->parseHardConstraints($userMessage);
        $topBooks = $this->retriever->retrieveRelevantBooks($userMessage, $embeddingLlm, $provider, 3);

        $result = $this->planner->execute(
            $llm,
            $userMessage,
            $conversation,
            $topBooks,
            $constraints
        );

        if (! isset($result['source'])) {
            $result['source'] = 'unknown';
        }

        return $result;
    }

    private function resolveModel(string $provider): LLMServiceInterface
    {
        $class = config("ai.providers.$provider");
        if (! $class || ! class_exists($class)) {
            $defaultProvider = (string) config('ai.provider', 'ollama');
            $class = config("ai.providers.$defaultProvider");
        }

        return App::make($class);
    }

    private function tryAuthorFactAnswer(string $message): ?array
    {
        if (! $this->parser->isAuthorFactQuery($message)) {
            return null;
        }

        $title = $this->parser->extractBookTitleFromAuthorQuery($message);
        if ($title === null || $title === '') {
            return [
                'answer' => 'Please share the book title so I can tell you the author.',
                'books' => [],
                'source' => 'catalog_fact',
            ];
        }

        $book = $this->retriever->findBookByTitle($title);
        if (! $book) {
            return [
                'answer' => "I couldn't find a book titled \"{$title}\" in the catalog.",
                'books' => [],
                'source' => 'catalog_fact',
            ];
        }

        $author = $book->author->name ?? 'Unknown';

        return [
            'answer' => "The author of \"{$book->name}\" is {$author}.",
            'books' => [
                ['book' => $book, 'score' => 1.0],
            ],
            'source' => 'catalog_fact',
        ];
    }

    private function tryBookPriceAnswer(string $message): ?array
    {
        if (! $this->parser->isBookPriceQuery($message)) {
            return null;
        }

        $title = $this->parser->extractBookTitleFromPriceQuery($message);
        if ($title === null || $title === '') {
            return [
                'answer' => 'Please share the book title so I can tell you the price.',
                'books' => [],
                'source' => 'catalog_price',
            ];
        }

        $book = $this->retriever->findBookByTitle($title);
        if (! $book) {
            return [
                'answer' => "I couldn't find a book titled \"{$title}\" in the catalog.",
                'books' => [],
                'source' => 'catalog_price',
            ];
        }

        $bestPrice = $this->retriever->bestAvailablePrice($book);
        $answer = sprintf(
            'The price of "%s" starts at INR %.2f.',
            $book->name,
            $bestPrice
        );

        return [
            'answer' => $answer,
            'books' => [
                ['book' => $book, 'score' => 1.0],
            ],
            'source' => 'catalog_price',
        ];
    }
}
