<div x-data="{ open: false }" class="chat-widget">

    <!-- Floating AI Button -->
    <button @click="open = !open" class="chat-button" aria-label="Open AI chat">
        <span aria-hidden="true">&#129302;</span>
    </button>

    <!-- Chat Window -->
    <div x-show="open" x-transition class="chat-window">
        <!-- Header -->
        <div class="chat-header">
            <span>AI Book Assistant</span>

             <select wire:model.live="model" class="model-select">
                @foreach($availableModels as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <button @click="open = false" class="close-btn" aria-label="Close chat">&times;</button>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages">
            @foreach($chatHistory ?? [] as $chat)
                <div class="chat-message">
                    <strong>{{ $chat['role'] }}:</strong>
                    {{ $chat['text'] }}
                </div>
            @endforeach
        </div>

        <!-- Input -->
        <div class="chat-input">
            <input
                type="text"
                wire:model="message"
                wire:keydown.enter="ask"
                placeholder="Ask for a book..."
                class="input-field"
            >

            <button wire:click="ask" class="send-btn">
                Send
            </button>
        </div>
    </div>

</div>
