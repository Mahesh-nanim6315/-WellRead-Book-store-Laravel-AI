<!DOCTYPE html>
<html>
<head>
    <title>My Library</title>
    @vite('resources/css/app.css')
</head>
<body>

@include('common.header')

<div class="library-container" style="margin-top: 100px;">

    <h1 class="library-title">📚 My Library</h1>

    @if($libraries->isEmpty())
        <p class="empty-text">You have no books in your library yet.</p>
    @else
        <div class="library-grid">
            @foreach($libraries as $item)

                @if(!$item->isExpired())
                <div class="library-card">

                    <img src="{{ $item->book->image }}" alt="{{ $item->book->name }}">

                    <h3>{{ $item->book->name }}</h3>

                    <span class="format-badge {{ $item->format }}">
                        {{ strtoupper($item->format) }}
                    </span>

                    {{-- ACTION BUTTONS --}}
                    @if($item->format === 'ebook')
                        <a href="{{ asset($item->book->ebook_pdf) }}" target="_blank"
                           class="btn read">📖 Read</a>

                    @elseif($item->format === 'audio')
                        <a href="{{ asset($item->book->audio_file) }}"
                           class="btn listen">🎧 Listen</a>

                    @else
                        <span class="owned">✔ Paperback Owned</span>
                    @endif

                    {{-- EXPIRY --}}
                    @if($item->expires_at)
                        <small class="expiry">
                            Expires on {{ $item->expires_at->format('d M Y') }}
                        </small>
                    @endif

                </div>
                @endif

            @endforeach
        </div>
    @endif

</div>

@include('common.footer')

</body>
</html>
