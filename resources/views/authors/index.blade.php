<!DOCTYPE html>
<html>
<head>
    <title>Authors</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@include('common.header')

<div class="container-auth" style="margin-top:120px;">

    <h1 class="page-title">📚 Our Authors</h1>

    <div class="authors-grid">
        @forelse($authors as $author)
            <div class="author-card">
                <img
                    src="{{ $author->image ?? asset('images/default-author.png') }}"
                    alt="{{ $author->name }}"
                >

                <h3>{{ $author->name }}</h3>

                <p class="bio">
                    {{ Str::limit($author->bio, 80) }}
                </p>

                <a href="{{ route('authors.show', $author->id) }}"
                   class="btn-view">
                    View Profile →
                </a>
            </div>
        @empty
            <p>No authors found.</p>
        @endforelse
    </div>

</div>

@include('common.footer')

</body>
</html>
