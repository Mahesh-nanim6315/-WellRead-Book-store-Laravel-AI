<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use App\Services\Contracts\LLMServiceInterface;

class OllamaService implements LLMServiceInterface
{
    protected string $baseUrl;
    protected string $model;
    protected string $embeddingModel;
    protected int $numPredict;

    public function __construct()
    {
        $this->baseUrl = (string) config('services.ollama.base_url', 'http://localhost:11434');
        $this->model = (string) config('services.ollama.model', 'llama3');
        $this->embeddingModel = (string) config('services.ollama.embedding_model', 'nomic-embed-text');
        $this->numPredict = (int) config('services.ollama.num_predict', 120);
    }

    public function generate(string $prompt): string
    {
        $response = Http::connectTimeout((int) config('ai.http_connect_timeout', 3))
            ->timeout((int) config('ai.http_timeout', 8))
            ->post($this->baseUrl . '/api/generate', [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'num_predict' => max(16, $this->numPredict),
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Ollama generate failed: ' . $response->body()
            );
        }

        return $response->json()['response'] ?? 'No response from Ollama.';
    }

    public function embedding(string $text): array
    {
        $response = Http::connectTimeout((int) config('ai.http_connect_timeout', 3))
            ->timeout((int) config('ai.http_timeout', 8))
            ->post($this->baseUrl . '/api/embeddings', [
                'model' => $this->embeddingModel,
                'prompt' => $text,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Ollama embedding failed: ' . $response->body()
            );
        }

        return $response->json()['embedding'] ?? [];
    }
}
