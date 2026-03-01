<?php

namespace App\Services;

use App\Models\DocumentChunk;

class DocumentVectorSearchService
{
    /**
     * @return array<int, array{chunk:\App\Models\DocumentChunk,score:float}>
     */
    public function search(
        array $queryEmbedding,
        int $limit = 5,
        float $minScore = 0.30,
        ?string $provider = null
    ): array {
        $query = DocumentChunk::query()->with('document')->whereNotNull('embedding');

        if ($provider !== null) {
            $query->where('embedding_provider', $provider);
        }

        $chunks = $query->get();
        $matches = [];

        foreach ($chunks as $chunk) {
            $embedding = json_decode((string) $chunk->embedding, true);
            if (! is_array($embedding) || count($embedding) !== count($queryEmbedding)) {
                continue;
            }

            $score = $this->cosineSimilarity($queryEmbedding, $embedding);
            if ($score < $minScore) {
                continue;
            }

            $matches[] = [
                'chunk' => $chunk,
                'score' => $score,
            ];
        }

        usort($matches, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($matches, 0, $limit);
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $count = count($a);
        for ($i = 0; $i < $count; $i++) {
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

