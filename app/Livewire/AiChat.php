<?php

namespace App\Livewire;

use App\Services\RagService;
use Livewire\Component;

class AiChat extends Component
{
    public string $message = '';
    public array $results = [];
    public ?string $reply = null;
    public array $chatHistory = [];
    public string $model = 'openai'; // default


    public array $availableModels = [
    'openai' => 'OpenAI',
    'gemini' => 'Gemini',
    'ollama' => 'Ollama (Local)',
    'huggingface' => 'Hugging Face',
];

   public function ask(RagService $rag): void
{
    $input = trim($this->message);
    if (! array_key_exists($this->model, $this->availableModels)) {
        $this->model = 'openai';
    }

    if ($input === '') {
        $this->reply = 'Type a message first.';
        $this->chatHistory[] = ['role' => 'assistant', 'text' => $this->reply];
        return;
    }

    try {
        $ragResponse = $rag->chat(
            $input,
            $this->chatHistory,
            $this->model // pass selected provider
        );

        $this->reply = (string) ($ragResponse['answer'] ?? 'No response');
        $this->results = (array) ($ragResponse['books'] ?? []);

        $this->chatHistory[] = ['role' => 'user', 'text' => $input];
        $this->chatHistory[] = ['role' => 'assistant', 'text' => $this->reply];

        if (count($this->chatHistory) > 20) {
            $this->chatHistory = array_slice($this->chatHistory, -20);
        }

    } catch (\Throwable $e) {
        \Log::error('AI Chat Error', ['error' => $e->getMessage()]);
        $this->reply = 'AI request failed. Please try again.';
        $this->results = [];
        $this->chatHistory[] = ['role' => 'user', 'text' => $input];
        $this->chatHistory[] = ['role' => 'assistant', 'text' => $this->reply];
    }

    $this->message = '';
}

    public function render()
    {
        return view('livewire.ai-chat');
    }
}
