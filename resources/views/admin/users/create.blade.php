@extends('admin.layouts.app')

@section('content')
<h2>Create User</h2>

<form action="{{ route('admin.users.store') }}" method="POST" class="form-box">
    @csrf

    <label>Name</label>
    <input type="text" name="name" value="{{ old('name') }}" required>

    <label>Email</label>
    <input type="email" name="email" value="{{ old('email') }}" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Role</label>
    <select name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="manager">Manager</option>
        <option value="staff">Staff</option>
    </select>

    <button type="submit" class="btn-primary">Create User</button>
</form>
@endsection
