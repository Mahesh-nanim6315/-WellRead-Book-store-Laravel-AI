@extends('admin.layouts.app')

@section('content')

<div class="page-header">
    <h2>Users</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add User</a>
</div>

<form method="GET" class="mysearch">
    <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}">
    <button id="bybtn" type="submit">Search</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : 'bg-info' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </td>
            <td>
                {{ $user->is_active ? 'Active' : 'Inactive' }}
            </td>
            <td>{{ $user->created_at->format('d M Y') }}</td>
            <td>
                <a href="{{ route('admin.users.edit', $user) }}">Edit</a>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Delete user?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $users->links() }}

@endsection

