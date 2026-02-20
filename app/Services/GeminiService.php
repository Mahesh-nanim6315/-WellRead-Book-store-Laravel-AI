<?php

namespace App\Services;

use App\Services\Contracts\LLMServiceInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService implements LLMServiceInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $textModel;
    private string $embeddingModel;

    public function __construct()
    {
        $this->apiKey = (string) (config('services.gemini.key') ?? env('GEMINI_API_KEY'));
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
        $this->textModel = (string) (config('services.gemini.text_model') ?? 'gemini-2.0-flash');
        $this->embeddingModel = (string) (config('services.gemini.embedding_model') ?? 'gemini-embedding-001');

        if ($this->apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }
    }

    public function embedding(string $text): array
    {
        $text = trim($text);

        if ($text === '') {
            return [];
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withHeader('X-goog-api-key', $this->apiKey)
            ->timeout(30)
            ->retry(2, 300)
            ->post(
                $this->baseUrl . $this->embeddingModel . ':embedContent',
                [
                    'content' => [
                        'parts' => [
                            ['text' => $text],
                        ],
                    ],
                ]
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'Gemini embedding request failed [' . $response->status() . ']: ' . $response->body()
            );
        }

        return data_get($response->json(), 'embedding.values', []);
    }

    public function generate(string $prompt): string
    {
        $prompt = trim($prompt);

        if ($prompt === '') {
            return 'No response';
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withHeader('X-goog-api-key', $this->apiKey)
            ->timeout(30)
            ->retry(2, 300)
            ->post(
                $this->baseUrl . $this->textModel . ':generateContent',
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                ]
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'Gemini text request failed [' . $response->status() . ']: ' . $response->body()
            );
        }

        return data_get(
            $response->json(),
            'candidates.0.content.parts.0.text',
            'No response'
        );
    }
}