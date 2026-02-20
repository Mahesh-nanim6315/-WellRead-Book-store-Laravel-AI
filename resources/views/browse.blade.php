 @vite(['resources/css/app.css', 'resources/js/app.js'])
<div class="browse-container">

@include('partials.carousel', [
    'title' => 'Anime / Manga',
    'books' => $anime,
    'category' => $animeCategory
])

@include('partials.carousel', [
    'title' => 'Fantasy',
    'books' => $fantasy,
    'category' => $fantasyCategory
])

@include('partials.carousel', [
    'title' => 'Comic',
    'books' => $comic,
    'category' => $comicCategory
])


    @include('partials.carousel', [
        'title' => 'Novels',
        'books' => $novel,
        'category' => $novelCategory
    ])

    @include('partials.carousel', [
        'title' => 'Sci-Fi',
        'books' => $scifi,
        'category' =>  $scifiCategory
    ])


</div> 
