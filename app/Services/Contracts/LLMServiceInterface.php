<?php

namespace App\Services\Contracts;

interface LLMServiceInterface
{
    public function generate(string $prompt): string;

    public function embedding(string $text): array;
}