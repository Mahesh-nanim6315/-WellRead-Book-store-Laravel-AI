<?php

return [
    'providers' => [
        'openai' => \App\Services\OpenAIService::class,
        'gemini' => \App\Services\GeminiService::class,
        'ollama' => \App\Services\OllamaService::class,
        'huggingface' => \App\Services\HuggingFaceService::class,
    ],
];