 @vite(['resources/css/app.css', 'resources/js/app.js'])
<div class="browse-container">

    @foreach($categories as $category)
        @include('partials.carousel', [
            'title' => $category->name,
            'books' => $category->books,
            'category' => $category
        ])
    @endforeach

</div> 
