
@vite(['resources/css/app.css', 'resources/js/app.js'])
@include('common.header')
<div class="browse-container" style="margin-top: 100px;">

    @include('partials.carousel', [
    'title' => 'Drama',
    'books' => $drama,
    'category' => $categories['Drama']
])

@include('partials.carousel', [
    'title' => 'Thriller',
    'books' => $thriller,
    'category' => $categories['Thriller']
])

@include('partials.carousel', [
    'title' => 'Social',
    'books' => $social,
    'category' => $categories['Social']
])

@include('partials.carousel', [
    'title' => 'Family',
    'books' => $family,
    'category' => $categories['Family']
])

@include('partials.carousel', [
    'title' => 'Romance',
    'books' => $romance,
    'category' => $categories['Romance']
])

</div>
@include('common.footer')