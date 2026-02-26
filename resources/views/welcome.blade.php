<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookVerse</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @include('common.header')
    @include('hero')
    @include('categories.filter')

    @if($recentlyViewedBooks->isNotEmpty())
    @include('partials.carousel', [
        'title' => 'Recently Viewed',
        'books' => $recentlyViewedBooks
        ])
    @endif

    @include('partials.carousel', [
        'title' => 'Recently Added',
        'books' => $recentBooks
    ])

    @include('partials.carousel', [
        'title' => 'Top Trending',
        'books' => $trendingBooks
    ])

    @include('browse')
    @include('newsletter.index')
    @include('common.footer')
    <livewire:ai-chat />
    @livewireScripts

</body>
</html>
