<!DOCTYPE html>
<html>
<head>
    <title>{{ $author->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@include('common.header')

<div class="container-show" style="margin-top:120px;">

    <!-- Author Info -->
    <div class="author-profile">
        <img
            src="{{ $author->image ?? asset('images/default-author.png') }}"
            alt="{{ $author->name }}"
        >

        <div class="author-info">
            <h1>{{ $author->name }}</h1>
            <p>{{ $author->bio }}</p>
        </div>
    </div>

    <hr>

    <!-- Author Books -->
    <h2>📖 Books by {{ $author->name }}</h2>

    <div class="books-grid">
        @forelse($author->books as $book)
            <div class="book-card">
                <img src="{{ $book->image }}" alt="{{ $book->name }}">

                <h4>{{ $book->name }}</h4>
                <p>₹{{ $book->price }}</p>

                <a href="{{ route('books.show', $book->id) }}"
                   class="btn-view">
                    View Book
                </a>
            </div>
        @empty
            <p>No books available for this author.</p>
        @endforelse
    </div>

    <a href="{{ route('authors.index') }}" class="btn-back">
        ← Back to Authors
    </a>

</div>

@include('common.footer')

</body>
</html>
