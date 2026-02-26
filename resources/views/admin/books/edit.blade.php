
@extends('admin.layouts.app')

@section('content')

<h2 style="text-align:center; margin-top:80px;">✏️ Edit Book</h2>

<div class="form-container">
<form action="{{ route('admin.books.update',$book->id) }}" method="POST">
@csrf
@method('PUT')

    <!-- BASIC INFO -->
    <h3>Basic Information</h3>

    <label>Book Name</label>
    <input type="text" name="name" value="{{ old('name', $book->name) }}" required>

    <!-- Author -->
       <select name="author_id" required>
       <option value="">Select Author</option>
       @foreach($authors as $author)
              <option value="{{ $author->id }}"
              {{ $book->author_id == $author->id ? 'selected' : '' }}>
              {{ $author->name }}
              </option>
       @endforeach
       </select>


       <!-- Genre -->
       <select name="genre_id" required>
       <option value="">Select Genre</option>
       @foreach($genres as $genre)
              <option value="{{ $genre->id }}"
              {{ $book->genre_id == $genre->id ? 'selected' : '' }}>
              {{ $genre->name }}
              </option>
       @endforeach
       </select>


       <!-- Category -->
       <select name="category_id" required>
       <option value="">Select Category</option>
       @foreach($categories as $category)
              <option value="{{ $category->id }}"
              {{ $book->category_id == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
              </option>
       @endforeach
       </select>


    <label>Description</label>
    <textarea name="description" rows="5" required>{{ old('description', $book->description) }}</textarea>

    <label>Language</label>
    <input type="text" name="language" value="{{ old('language', $book->language) }}">


    <label>Image URL</label>
    <input type="url" name="image" value="{{ old('image', $book->image) }}" required>

    <label style="margin-top: 10px;">
        <input type="checkbox" name="is_premium" value="1"
            {{ old('is_premium', $book->is_premium) ? 'checked' : '' }}>
        Premium book (requires subscription)
    </label>

    <div style="margin:10px 0;">
        <strong>Preview:</strong><br>
        <img src="{{ $book->image }}" width="120">
    </div>

    <hr>

    <!-- EBOOK -->
    <h3>📘 eBook</h3>

    <label>
        <input type="checkbox" name="has_ebook" value="1"
            {{ old('has_ebook', $book->has_ebook) ? 'checked' : '' }}>
        Available as eBook
    </label>

    <label>eBook Price</label>
    <input type="number" step="0.01" name="ebook_price"
           value="{{ old('ebook_price', $book->ebook_price) }}">

    <label>eBook PDF URL</label>
    <input type="text" name="ebook_pdf"
           value="{{ old('ebook_pdf', $book->ebook_pdf) }}">

    <label>eBook Pages</label>
    <input type="number" name="ebook_pages"
           value="{{ old('ebook_pages', $book->ebook_pages) }}">

    <hr>

    <!-- AUDIO -->
    <h3>🎧 Audio Book</h3>

    <label>
        <input type="checkbox" name="has_audio" value="1"
            {{ old('has_audio', $book->has_audio) ? 'checked' : '' }}>
        Available as Audio Book
    </label>

    <label>Audio Price</label>
    <input type="number" step="0.01" name="audio_price"
           value="{{ old('audio_price', $book->audio_price) }}">

    <label>Audio File URL</label>
    <input type="text" name="audio_file"
           value="{{ old('audio_file', $book->audio_file) }}">

    <label>Audio Duration (minutes)</label>
    <input type="number" name="audio_minutes"
           value="{{ old('audio_minutes', $book->audio_minutes) }}">

    <hr>

    <!-- PAPERBACK -->
    <h3>📕 Paperback</h3>

    <label>
        <input type="checkbox" name="has_paperback" value="1"
            {{ old('has_paperback', $book->has_paperback) ? 'checked' : '' }}>
        Available as Paperback
    </label>

    <label>Paperback Price</label>
    <input type="number" step="0.01" name="paperback_price"
           value="{{ old('paperback_price', $book->paperback_price) }}">

    <label>Paperback Pages</label>
    <input type="number" name="paperback_pages"
           value="{{ old('paperback_pages', $book->paperback_pages) }}">

    <label>Stock Available</label>
    <input type="number" name="stock"
           value="{{ old('stock', $book->stock) }}">

    <hr>
       <h3>⚠ Legacy Price</h3>
     <label>Main Price</label>
     <input type="number" name="price"
            value="{{old('price', $book->price)}}"> 

    <button type="submit">💾 Update Book</button>
</form>
</div>

@endsection

