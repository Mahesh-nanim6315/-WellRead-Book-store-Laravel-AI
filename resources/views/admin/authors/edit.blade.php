@extends('admin.layouts.app')

@section('content')

<h2>Edit Author</h2>

<form action="{{ route('admin.authors.update',$author->id) }}"
      method="POST" class="form-box">
@csrf
@method('PUT')

<label>Name</label>
<input type="text" name="name"
       value="{{ old('name',$author->name) }}" required>

<label>Image URL</label>
<input type="text" name="image"
       value="{{ old('image',$author->image) }}">

@if($author->image)
    <img src="{{ $author->image }}"
         style="width:100px;margin:10px 0;">
@endif

<label>Bio</label>
<textarea name="bio" rows="5">
{{ old('bio',$author->bio) }}
</textarea>

<button type="submit" class="btn-primary">Update Author</button>

</form>

@endsection
