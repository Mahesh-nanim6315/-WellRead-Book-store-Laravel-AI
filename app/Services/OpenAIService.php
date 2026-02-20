<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use App\Services\Contracts\LLMServiceInterface;

class OpenAIService implements LLMServiceInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $chatModel;
    protected string $embeddingModel;

    public function __construct()
    {
        $this->baseUrl = 'https://api.openai.com/v1';
        $this->apiKey = (string) config('services.openai.key', '');

        $this->chatModel = (string) config('services.openai.chat_model', 'gpt-4o-mini');
        $this->embeddingModel = (string) config('services.openai.embedding_model', 'text-embedding-3-small');

        if ($this->apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }
    }

    public function generate(string $prompt): string
    {
        $response = Http::timeout(120)
            ->retry(2, 500)
            ->withToken($this->apiKey)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $this->chatModel,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'OpenAI chat failed: ' . $response->body()
            );
        }

        return $response->json()['choices'][0]['message']['content'] ?? '';
    }

    public function embedding(string $text): array
    {
        $response = Http::timeout(120)
            ->retry(2, 500)
            ->withToken($this->apiKey)
            ->post($this->baseUrl . '/embeddings', [
                'model' => $this->embeddingModel,
                'input' => $text,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'OpenAI embedding failed: ' . $response->body()
            );
        }

        return $response->json()['data'][0]['embedding'] ?? [];
    }
}
