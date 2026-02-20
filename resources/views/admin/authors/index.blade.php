@extends('admin.layouts.app')

@section('content')

<div class="page-header">
    <h2>Authors</h2>
    <a href="{{ route('admin.authors.create') }}" class="btn-primary">
        + Add Author
    </a>
</div>

<form method="GET" action="{{ route('admin.authors.index') }}" class="filter-box">

    <input type="text"
           name="search"
           placeholder="Search author..."
           value="{{ request('search') }}">

    <input type="number"
           name="min_books"
           placeholder="Min books"
           value="{{ request('min_books') }}"
           min="0">

    <button type="submit" class="btn-primary">Filter</button>

    <a href="{{ route('admin.authors.index') }}"
       class="btn-secondary">Reset</a>

</form>

<hr>

<table class="table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Total Books</th>
            <th>Bio</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($authors as $author)
        <tr>
            <td>
                @if($author->image)
                    <img src="{{ $author->image }}" width="60">
                @endif
            </td>

            <td>{{ $author->name }}</td>

             <td>
                <strong>{{ $author->books_count }}</strong>
            </td>

            <td>{{ Str::limit($author->bio, 50) }}</td>

            <td style="display: flex;">
                <a href="{{ route('admin.authors.show',$author->id) }}">View</a>
                <a href="{{ route('admin.authors.edit',$author->id) }}">Edit</a>

                <form action="{{ route('admin.authors.destroy',$author->id) }}"
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

{{ $authors->links() }}

@endsection
