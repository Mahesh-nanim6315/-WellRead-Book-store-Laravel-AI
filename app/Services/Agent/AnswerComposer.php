<?php

namespace App\Services\Agent;

use App\Services\Contracts\LLMServiceInterface;
use Throwable;

class AnswerComposer
{
    public function buildConversationContext(array $history, int $maxTurns = 6): string
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

        return empty($lines) ? 'No prior conversation.' : implode("\n", $lines);
    }

    public function buildBookContext(array $rows): string
    {
        $lines = [];

        foreach ($rows as $row) {
            $book = $row['book'];
            $description = trim((string) $book->description);
            if (strlen($description) > 180) {
                $description = substr($description, 0, 177) . '...';
            }
            $lines[] = sprintf(
                "- Title: %s | Author: %s | Category: %s | Genre: %s | Description: %s | Score: %.4f",
                $book->name,
                $book->author->name ?? 'Unknown',
                $book->category->name ?? 'Unknown',
                $book->genre->name ?? 'Unknown',
                $description,
                (float) ($row['score'] ?? 0.0)
            );
        }

        return implode("\n", $lines);
    }

    public function buildBookContextCompact(array $rows, BookRetriever $retriever): string
    {
        if (empty($rows)) {
            return 'No retrieved books.';
        }

        $lines = [];
        foreach (array_slice($rows, 0, 10) as $row) {
            $book = $row['book'];
            $lines[] = sprintf(
                '%d | %s | %s | %s | %s | best_price=%.2f',
                $book->id,
                $book->name,
                $book->author->name ?? 'Unknown',
                $book->category->name ?? 'Unknown',
                $book->genre->name ?? 'Unknown',
                $retriever->bestAvailablePrice($book)
            );
        }

        return implode("\n", $lines);
    }

    public function buildAgentFinalAnswer(
        LLMServiceInterface $llm,
        string $userMessage,
        string $conversation,
        array $rows,
        array $observations,
        ?bool &$usedFallback = null
    ): string {
        $usedFallback = false;
        $bookContext = $this->buildBookContext($rows);
        $obsJson = json_encode($this->compactObservations($observations), JSON_PRETTY_PRINT) ?: '[]';

        $prompt = <<<PROMPT
You are a helpful AI assistant for a bookstore app.
Use catalog books only when the user is asking for books.

Conversation memory:
{$conversation}

User query:
{$userMessage}

Catalog candidates:
{$bookContext}

Tool observations:
{$obsJson}

Instructions:
- First detect intent from the user query.
- If the user asks for book recommendations/search, recommend up to 3 catalog books and do not invent titles.
- If the user asks a general question (study tips, habits, motivation, explanations), answer directly and do NOT force book recommendations.
- If the user requests a specific format/length (for example: "2 lines", "3 bullet points"), follow it exactly.
- Do not say you cannot browse or check physical books unless the user explicitly asks for real-time store stock verification.
- Mention stock/price constraints only when relevant.
- Keep concise and friendly.
PROMPT;

        try {
            return $llm->generate($prompt);
        } catch (Throwable $e) {
            $usedFallback = true;
            return $this->buildAgentFallbackAnswer($rows, $observations);
        }
    }

    public function buildAgentFallbackAnswer(array $rows, array $observations): string
    {
        if (empty($rows)) {
            return 'I could not find matching books right now. Try adding a genre, author, or price range.';
        }

        $lines = [
            'Based on current catalog data, here are suggested matches:',
            '(These are recommendations from your store catalog.)',
        ];
        foreach (array_slice($rows, 0, 3) as $row) {
            $book = $row['book'];
            $lines[] = sprintf(
                '- %s by %s (%s, %s)',
                $book->name,
                $book->author->name ?? 'Unknown',
                $book->category->name ?? 'Unknown',
                $book->genre->name ?? 'Unknown'
            );
        }

        if (! empty($observations)) {
            $lines[] = 'I also used tool checks for filtering/availability.';
        }

        return implode("\n", $lines);
    }

    public function buildNoConstraintMatchAnswer(array $constraints): string
    {
        $currency = strtoupper((string) ($constraints['currency'] ?? ''));
        $parts = [];
        if (($constraints['language'] ?? null) !== null) {
            $parts[] = 'language=' . $constraints['language'];
        }
        if (($constraints['min_price'] ?? null) !== null) {
            $parts[] = 'min_price=' . $this->formatPrice((float) $constraints['min_price'], $currency);
        }
        if (($constraints['max_price'] ?? null) !== null) {
            $parts[] = 'max_price=' . $this->formatPrice((float) $constraints['max_price'], $currency);
        }
        if (($constraints['mood'] ?? null) !== null) {
            $parts[] = 'mood=' . $constraints['mood'];
        }

        $summary = empty($parts) ? 'your constraints' : implode(', ', $parts);

        return "I could not find books that satisfy all constraints ({$summary}). Try increasing budget or relaxing mood/language filters.";
    }

    private function formatPrice(float $value, string $currency): string
    {
        if ($currency === 'INR') {
            return 'INR ' . number_format($value, 2);
        }

        if ($currency === 'USD') {
            return '$' . number_format($value, 2);
        }

        return number_format($value, 2);
    }

    public function compactObservations(array $observations): array
    {
        $rows = [];
        foreach ($observations as $obs) {
            $rows[] = [
                'step' => $obs['step'] ?? null,
                'tool' => $obs['tool'] ?? null,
                'args' => $obs['args'] ?? [],
                'result' => $obs['result'] ?? [],
            ];
        }

        return array_slice($rows, -6);
    }
}
