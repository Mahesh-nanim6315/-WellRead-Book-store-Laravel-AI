 @vite(['resources/css/app.css', 'resources/js/app.js'])
<section class="carousel-section">

 <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>{{ $title }}</h2>

        @if(isset($category) && $category)
            <a href="{{ route('category.books', $category->slug) }}"
               class="view-all-btn">
                View All
            </a>
        @endif
    </div>

    <div class="carousel-wrapper">
        <button class="nav-btn left" onclick="slideLeft(this)">❮</button>

        <div class="carousel-track">
            @foreach($books as $book)
                <div class="carousel-card">
                    <a href="{{ route('books.show', $book->id) }}">
                        <img src="{{ $book->image }}" width="200" height="200">
                        <p class="card-title">{{ $book->name }}</p>
                    </a>
                </div>
            @endforeach

        </div>

        <button class="nav-btn right" onclick="slideRight(this)">❯</button>
    </div>
</section>
