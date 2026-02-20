@extends('admin.layouts.app')

@section('content')

<div class="author-detail">
    <h2>{{ $author->name }}</h2>

    @if($author->image)
        <img src="{{ $author->image }}" width="150" class="author-image">
    @endif

    <p class="author-bio">
        {{ $author->bio }}
    </p>

    <a href="{{ route('admin.authors.edit',$author->id) }}"
       class="btn-primary">Edit</a>
</div>

@endsection
