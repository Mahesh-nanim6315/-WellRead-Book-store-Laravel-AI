<?php

namespace App\Services;

use App\Models\Book;

class BookVectorSearchService
{
    public function search(
        array $queryEmbedding,
        int $limit = 3,
        float $minScore = 0.50,
        ?string $provider = null
    ): array
    {
        $query = Book::with(['author', 'category', 'genre'])
            ->whereNotNull('embedding');

        if ($provider !== null) {
            $query->where('embedding_provider', $provider);
        }

        $books = $query->get();

        $similarities = [];

        foreach ($books as $book) {
            $bookEmbedding = json_decode($book->embedding, true);
            if (! is_array($bookEmbedding) || count($bookEmbedding) !== count($queryEmbedding)) {
                continue;
            }

            $score = $this->cosineSimilarity($queryEmbedding, $bookEmbedding);

            if ($score >= $minScore) {
                $similarities[] = [
                    'book' => $book,
                    'score' => $score,
                ];
            }
        }

        usort($similarities, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($similarities, 0, $limit);
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
