<?php

namespace App\Services;

use App\Models\Document;
use App\Services\Contracts\LLMServiceInterface;
use Illuminate\Support\Facades\App;
use RuntimeException;

class DocumentIngestionService
{
    public function __construct(
        private readonly DocumentExtractorService $extractor,
    ) {
    }

    /**
     * @return array{document:Document,chunks:int}
     */
    public function ingest(
        string $path,
        ?string $provider = null,
        ?string $sourceType = null
    ): array {
        $provider = $provider ?: (string) config('ai.embedding_provider', config('ai.provider', 'ollama'));
        $sourceType = $sourceType ?: 'upload';

        $llm = $this->resolveModel($provider);
        $segments = $this->extractor->extract($path);

        if (empty($segments)) {
            throw new RuntimeException('No extractable text found in document.');
        }

        $doc = Document::create([
            'title' => pathinfo($path, PATHINFO_FILENAME),
            'file_path' => $path,
            'source_type' => $sourceType,
        ]);

        $chunkSize = max(200, (int) config('ai.docs.chunk_size', 700));
        $overlap = max(0, (int) config('ai.docs.chunk_overlap', 120));

        $index = 0;
        foreach ($segments as $segment) {
            $pageNo = $segment['page_no'] ?? null;
            $chunks = $this->chunkText((string) ($segment['text'] ?? ''), $chunkSize, $overlap);

            foreach ($chunks as $chunk) {
                $embedding = $llm->embedding($chunk);
                $doc->chunks()->create([
                    'chunk_index' => $index++,
                    'page_no' => $pageNo,
                    'chunk_text' => $chunk,
                    'embedding' => empty($embedding) ? null : json_encode($embedding),
                    'embedding_provider' => $provider,
                ]);
            }
        }

        return [
            'document' => $doc->fresh(),
            'chunks' => $index,
        ];
    }

    private function resolveModel(string $provider): LLMServiceInterface
    {
        $class = config("ai.providers.$provider");
        if (! $class || ! class_exists($class)) {
            throw new RuntimeException("Invalid embedding provider: {$provider}");
        }

        return App::make($class);
    }

    /**
     * @return array<int, string>
     */
    private function chunkText(string $text, int $size, int $overlap): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }

        $chunks = [];
        $start = 0;
        $len = mb_strlen($text);
        $step = max(1, $size - $overlap);

        while ($start < $len) {
            $chunk = mb_substr($text, $start, $size);
            $chunk = trim($chunk);
            if ($chunk !== '') {
                $chunks[] = $chunk;
            }
            $start += $step;
        }

        return $chunks;
    }
}

