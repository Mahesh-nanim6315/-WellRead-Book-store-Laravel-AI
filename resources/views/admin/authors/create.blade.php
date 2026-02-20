@extends('admin.layouts.app')

@section('content')

<h2>Add Author</h2>

<form action="{{ route('admin.authors.store') }}" method="POST" class="form-box">
@csrf

<label>Name</label>
<input type="text" name="name" required>

<label>Image URL</label>
<input type="text" name="image">

<label>Bio</label>
<textarea name="bio" rows="5"></textarea>

<button type="submit" class="btn-primary">Create Author</button>

</form>

@endsection
