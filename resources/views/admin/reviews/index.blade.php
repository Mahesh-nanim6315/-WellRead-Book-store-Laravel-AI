@extends('admin.layouts.app')

@section('content')

<h2>Reviews Management</h2>

<form method="GET" class="filter-box">

    <input type="text"
           name="search"
           placeholder="Search by book or user"
           value="{{ request('search') }}">

    <select name="status">
        <option value="">All</option>
        <option value="0"
            {{ request('status') === "0" ? 'selected' : '' }}>
            Pending
        </option>
        <option value="1"
            {{ request('status') === "1" ? 'selected' : '' }}>
            Approved
        </option>
    </select>

    <button type="submit" class="btn-primary">Filter</button>
    <a href="{{ route('admin.reviews.index') }}"
       class="btn-secondary">Reset</a>
</form>

<hr>

<table class="table">
    <thead>
        <tr>
            <th>User</th>
            <th>Book</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @forelse($reviews as $review)
        <tr>
            <td>{{ $review->user->name }}</td>
            <td>{{ $review->book->name }}</td>
            <td>⭐ {{ $review->rating }}/5</td>
            <td>{{ Str::limit($review->comment, 60) }}</td>
            <td>
                @if($review->is_approved)
                    <span style="color:green;">Approved</span>
                @else
                    <span style="color:red;">Pending</span>
                @endif
            </td>

            <td>
                <form action="{{ route('admin.reviews.approve',$review->id) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit">
                        {{ $review->is_approved ? 'Reject' : 'Approve' }}
                    </button>
                </form>

                <form action="{{ route('admin.reviews.destroy',$review->id) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">No reviews found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{ $reviews->links() }}

@endsection
