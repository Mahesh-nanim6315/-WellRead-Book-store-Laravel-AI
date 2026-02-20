@extends('admin.layouts.app')

@section('content')
<h2>Edit User</h2>

<form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="form-box">
    @csrf
    @method('PUT')

    <label>Name</label>
    <input type="text" name="name" value="{{ $user->name }}" required>

    <label>Email</label>
    <input type="email" name="email" value="{{ $user->email }}" required>

    <label>New Password (optional)</label>
    <input type="password" name="password">

    <label>Role</label>
    <select name="role" required>
        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Manager</option>
        <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
    </select>

    <button type="submit" class="btn-primary">Update User</button>
</form>
@endsection
