<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\RagService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AiChat extends Component
{
    private const HISTORY_LIMIT = 50;

    public string $message = '';
    public array $results = [];
    public ?string $reply = null;
    public array $chatHistory = [];
    public string $model = 'ollama';
    public ?int $chatSessionId = null;


    public array $availableModels = [
    'gemini' => 'Gemini',
    'ollama' => 'Ollama (Local)',
    'huggingface' => 'Hugging Face',
];

    public function mount(): void
    {
        $session = $this->resolveActiveSession();
        $this->chatSessionId = (int) $session->id;
        session(['ai_chat_session_id' => $this->chatSessionId]);
        $this->hydrateHistoryFromDatabase();
    }

    public function newChat(): void
    {
        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'title' => 'New Chat',
        ]);

        $this->chatSessionId = (int) $session->id;
        session(['ai_chat_session_id' => $this->chatSessionId]);

        $this->chatHistory = [];
        $this->results = [];
        $this->reply = null;
        $this->message = '';
    }

    public function ask(RagService $rag): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit((int) config('ai.http_timeout', 150) + 20);
        }
        @ini_set('max_execution_time', (string) ((int) config('ai.http_timeout', 150) + 20));

        $input = trim($this->message);
        if (! array_key_exists($this->model, $this->availableModels)) {
            $this->model = 'ollama';
        }

        if ($input === '') {
            $this->reply = 'Type a message first.';
            return;
        }

        $session = $this->resolveActiveSession();
        $this->chatSessionId = (int) $session->id;
        session(['ai_chat_session_id' => $this->chatSessionId]);

        $userMessage = ['role' => 'user', 'text' => $input];
        $this->chatHistory[] = $userMessage;
        $this->persistMessage($session, 'user', $input, null);
        $historyForRag = array_slice($this->chatHistory, 0, -1);

        try {
            $ragResponse = $rag->chat(
                $input,
                $historyForRag,
                $this->model
            );

            $this->reply = (string) ($ragResponse['answer'] ?? 'No response');
            $this->results = (array) ($ragResponse['books'] ?? []);
            $source = (string) ($ragResponse['source'] ?? 'unknown');

            $assistantMessage = [
                'role' => 'assistant',
                'text' => $this->reply,
                'source' => $source,
            ];

            $this->chatHistory[] = $assistantMessage;
            $this->persistMessage($session, 'assistant', $this->reply, $source, [
                'model' => $this->model,
            ]);
        } catch (\Throwable $e) {
            \Log::error('AI Chat Error', ['error' => $e->getMessage()]);
            $this->reply = 'AI request failed. Please try again.';
            $this->results = [];

            $assistantMessage = [
                'role' => 'assistant',
                'text' => $this->reply,
                'source' => 'system_error',
            ];

            $this->chatHistory[] = $assistantMessage;
            $this->persistMessage($session, 'assistant', $this->reply, 'system_error');
        }

        if (count($this->chatHistory) > self::HISTORY_LIMIT) {
            $this->chatHistory = array_slice($this->chatHistory, -self::HISTORY_LIMIT);
        }

        $this->message = '';
    }

    private function resolveActiveSession(): ChatSession
    {
        $sessionId = session('ai_chat_session_id');
        $userId = Auth::id();

        if (is_numeric($sessionId)) {
            $query = ChatSession::query()->whereKey((int) $sessionId);
            if ($userId === null) {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $userId);
            }

            $existing = $query->first();
            if ($existing) {
                return $existing;
            }
        }

        if ($userId !== null) {
            $latestUserSession = ChatSession::query()
                ->where('user_id', $userId)
                ->latest('updated_at')
                ->first();

            if ($latestUserSession) {
                return $latestUserSession;
            }
        }

        return ChatSession::create([
            'user_id' => $userId,
            'title' => 'New Chat',
        ]);
    }

    private function hydrateHistoryFromDatabase(): void
    {
        if (! is_numeric($this->chatSessionId)) {
            $this->chatHistory = [];
            return;
        }

        $messages = ChatMessage::query()
            ->where('chat_session_id', (int) $this->chatSessionId)
            ->orderByDesc('id')
            ->limit(self::HISTORY_LIMIT)
            ->get()
            ->reverse()
            ->values();

        $this->chatHistory = $messages
            ->map(fn (ChatMessage $message) => [
                'role' => $message->role,
                'text' => $message->text,
                'source' => $message->source,
            ])
            ->all();
    }

    private function persistMessage(ChatSession $session, string $role, string $text, ?string $source = null, array $meta = []): void
    {
        $session->messages()->create([
            'role' => $role,
            'text' => $text,
            'source' => $source,
            'meta' => empty($meta) ? null : $meta,
        ]);

        $session->touch();
    }

    public function render()
    {
        return view('livewire.ai-chat');
    }
}
