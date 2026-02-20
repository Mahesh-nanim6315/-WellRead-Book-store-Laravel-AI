@extends('admin.layouts.app')

@section('content')
    <div class="page-header">
          <h2>Books</h2>
          <a href="{{ route('admin.books.create') }}" class="btn-primary">+ Add Book</a>  
    </div>

    
<hr>

<form method="GET" action="{{ route('admin.books.index') }}" class="filter-box">

    <input type="text" name="search"
        placeholder="Search book..."
        value="{{ request('search') }}">

    <select name="author">
        <option value="">All Authors</option>
        @foreach($authors as $author)
            <option value="{{ $author->id }}"
                {{ request('author') == $author->id ? 'selected' : '' }}>
                {{ $author->name }}
            </option>
        @endforeach
    </select>

    <select name="category">
        <option value="">All Categories</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ request('category') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="genre">
        <option value="">All Genres</option>
        @foreach($genres as $genre)
            <option value="{{ $genre->id }}"
                {{ request('genre') == $genre->id ? 'selected' : '' }}>
                {{ $genre->name }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="btn-primary">Filter</button>
    <a href="{{ route('admin.books.index') }}" class="btn-secondary">Reset</a>

</form>

<hr>


<table class="table">
    <thead>
        <tr>
            <th>Book Name</th>
            <th>Author</th>
            <th>Category</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($books as $book)
        <tr>
            <td>{{ $book->name }}</td>
            <td>{{ $book->author->name ?? '-' }}</td>
            <td>{{ $book->category->name ?? '-' }}</td>
            <td>${{ $book->price }}</td>
            <td style="display: flex;">
                <a href="{{ route('admin.books.edit',$book->id) }}">Edit</a>

                <form action="{{ route('admin.books.destroy',$book->id) }}"
                      method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $books->links() }}

@endsection
