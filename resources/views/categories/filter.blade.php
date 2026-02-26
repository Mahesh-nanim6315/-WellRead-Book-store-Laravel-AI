@vite(['resources/css/app.css', 'resources/js/app.js'])
<section class="category-buttons">

    <h2 style="margin-left: 1rem; margin-bottom: 1rem; margin-top: 1rem;">Browse Categories</h2>

    <div class="category-list" style="margin-left: 15px;">
        @foreach($categories as $category)
            <a href="{{ route('category.books', $category->slug) }}" 
               class="category-btn">
                {{ $category->name }}
            </a>
        @endforeach

        <a href="{{ route('categories.index') }}" class="view-all-btn">
            View All
        </a>
    </div>

</section>