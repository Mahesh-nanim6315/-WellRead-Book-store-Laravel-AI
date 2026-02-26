@extends('admin.layouts.app')

@section('content')
<h2 style="text-align:center;">📚 Add New Book</h2>

<div class="form-container">
<form action="{{ route('admin.books.store') }}" method="POST">
@csrf


<h3>Basic Information</h3>

<label>Book Name</label>
<input type="text" name="name" required>

<select name="author_id" required>
    <option value="">Select Author</option>
    @foreach($authors as $author)
        <option value="{{ $author->id }}">{{ $author->name }}</option>
    @endforeach
</select>

<select name="genre_id" required>
    <option value="">Select Genre</option>
    @foreach($genres as $genre)
        <option value="{{ $genre->id }}">{{ $genre->name }}</option>
    @endforeach
</select>


<select name="category_id" required>
    <option value="">Select Category</option>
    @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
    @endforeach
</select>


<label>Description</label>
<textarea name="description" rows="5" required></textarea>

<label>Language</label>
<input type="text" name="language" placeholder="English, Telugu, Hindi" required>


<label>Image URL</label>
<input type="text" name="image" placeholder="https://..." required>

<label style="margin-top: 10px;">
    <input type="checkbox" name="is_premium" value="1">
    Premium book (requires subscription)
</label>

<hr>


<label>
    <input type="checkbox" id="has_ebook" name="has_ebook" value="1">
    📘 Available as eBook
</label>

<div id="ebookBox" class="section-box">
    <label>eBook Price</label>
    <input type="number" name="ebook_price" step="0.01">

    <label>eBook PDF URL</label>
    <input type="text" name="ebook_pdf">

    <label>eBook Pages</label>
    <input type="number" name="ebook_pages">
</div>

<hr>


<label>
    <input type="checkbox" id="has_audio" name="has_audio" value="1">
    🎧 Available as Audio Book
</label>

<div id="audioBox" class="section-box">
    <label>Audio Price</label>
    <input type="number" name="audio_price" step="0.01">

    <label>Audio File URL</label>
    <input type="text" name="audio_file">

    <label>Audio Duration (minutes)</label>
    <input type="number" name="audio_minutes">
</div>

<hr>


<label>
    <input type="checkbox" id="has_paperback" name="has_paperback" value="1">
    📕 Available as Paperback
</label>

<div id="paperBox" class="section-box">
    <label>Paperback Price</label>
    <input type="number" name="paperback_price" step="0.01">

    <label>Paperback Pages</label>
    <input type="number" name="paperback_pages">

    <label>Stock Available</label>
    <input type="number" name="stock">

  
</div>

<hr>
     <h3>⚠ Legacy Price</h3>
     <label>Main Price</label>
     <input type="number" name="price"> 

<button type="submit">➕ Add Book</button>

</form>
</div>

@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {
    function toggleBox(checkboxId, boxId) {
        const checkbox = document.getElementById(checkboxId);
        const box = document.getElementById(boxId);

        if (checkbox && box) {
            checkbox.addEventListener('change', function () {
                box.classList.toggle('active', this.checked);
            });
        }
    }

    toggleBox('has_ebook', 'ebookBox');
    toggleBox('has_audio', 'audioBox');
    toggleBox('has_paperback', 'paperBox');
});
</script>





