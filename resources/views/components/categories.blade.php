<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>
<body>
    <div class="radial-menu">
    <button class="center-btn" id="toggleMenu">Top categories</button>

    <div class="menu-item item1"><a href="/category/anime">Anime</a></div>
    <div class="menu-item item2"><a href="/category/manga">Manga</a></div>
    <div class="menu-item item3"><a href="/category/comic">Comics</a></div>
    <div class="menu-item item4"><a href="/category/novels">Novels</a></div>
    <div class="menu-item item5"><a href="/category/fantasy">Fantasy</a></div>
    <div class="menu-item item6"><a href="/category/sci-fi">Sci-Fi</a></div>
</div>
</body>
</html>


