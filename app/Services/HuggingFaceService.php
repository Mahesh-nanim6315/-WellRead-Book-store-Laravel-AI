<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use App\Services\Contracts\LLMServiceInterface;

class HuggingFaceService implements LLMServiceInterface
{
    protected string $routerBaseUrl = 'https://router.huggingface.co/hf-inference/models/';
    protected string $chatModel;
    protected string $embeddingModel;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = (string) config('services.huggingface.key', '');
        $this->chatModel = (string) config(
            'services.huggingface.chat_model',
            'mistralai/Mistral-7B-Instruct-v0.2'
        );
        $this->embeddingModel = (string) config(
            'services.huggingface.embedding_model',
            'sentence-transformers/all-MiniLM-L6-v2'
        );

        if ($this->apiKey === '') {
            throw new RuntimeException('HUGGINGFACE_API_KEY is not configured.');
        }
    }

    public function generate(string $prompt): string
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->retry(2, 500)
            ->post($this->routerBaseUrl . $this->chatModel, [
                'inputs' => $prompt,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'HuggingFace generate failed [' . $response->status() . ']: ' . $response->body()
            );
        }

        $data = $response->json();

        if (isset($data['error'])) {
            throw new RuntimeException('HuggingFace error: ' . $data['error']);
        }

        return $data[0]['generated_text'] ?? 'No response.';
    }

    public function embedding(string $text): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->retry(2, 500)
            ->post($this->routerBaseUrl . $this->embeddingModel, [
                'inputs' => $text,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'HuggingFace embedding failed [' . $response->status() . ']: ' . $response->body()
            );
        }

        $data = $response->json();

        if (isset($data['error'])) {
            throw new RuntimeException('HuggingFace embedding error: ' . $data['error']);
        }

        if (! is_array($data)) {
            return [];
        }

        // Some HF models return [[...]] for single-input embeddings.
        if (isset($data[0]) && is_array($data[0]) && isset($data[0][0]) && is_numeric($data[0][0])) {
            return $data[0];
        }

        return $data;
    }
}
