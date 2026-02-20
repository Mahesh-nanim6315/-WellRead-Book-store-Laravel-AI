@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
        <h2>Book Details</h2>
        <a href="{{ route('admin.books.index') }}" class="btn-secondary">Back</a>
    </div>

    <hr>

    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:16px;">
        <p><strong>Name:</strong> {{ $book->name }}</p>
        <p><strong>Author:</strong> {{ $book->author->name ?? '-' }}</p>
        <p><strong>Category:</strong> {{ $book->category->name ?? '-' }}</p>
        <p><strong>Genre:</strong> {{ $book->genre->name ?? '-' }}</p>
        <p><strong>Language:</strong> {{ $book->language }}</p>
        <p><strong>Price:</strong> ${{ $book->price }}</p>
        <p><strong>Description:</strong> {{ $book->description }}</p>
    </div>
@endsection
