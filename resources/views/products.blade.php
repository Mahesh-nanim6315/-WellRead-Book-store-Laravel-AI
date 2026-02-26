<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="products-page">

@include('common.header')

<h1 class="page-title" style="margin-top: 100px;">Pick Top Seller</h1>

<div class="container">

    <div class="filter-box">
        <form method="GET" action="{{ route('products.home') }}">

        
            <label><strong>Search</strong></label>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search book...">

          
            <label><strong>Category</strong></label>
            <select name="category_id">
                <option value="">All</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <label><strong>Language</strong></label>
            <select name="language">
                <option value="">All</option>
                @foreach($languages as $language)
                    <option value="{{ $language }}"
                        {{ request('language') == $language ? 'selected' : '' }}>
                        {{ $language }}
                    </option>
                @endforeach
            </select>

            <label><strong>Author</strong></label>
            <select name="author_id">
                <option value="">All</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}"
                        {{ request('author_id') == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>

            <label><strong>Genre</strong></label>
            <select name="genre_id">
                <option value="">All</option>
                @foreach($genres as $genre)
                    <option value="{{ $genre->id }}"
                        {{ request('genre_id') == $genre->id ? 'selected' : '' }}>
                        {{ $genre->name }}
                    </option>
                @endforeach
            </select>

         
            <label><strong>Sort by Price</strong></label>
            <select name="sort">
                <option value="">Default</option>
                <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>
                    Low to High
                </option>
                <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>
                    High to Low
                </option>
            </select>

            <button type="submit">Apply Filters</button>
        </form>
    </div>

    <!-- RIGHT SIDE PRODUCTS -->
    <div class="products-section">

        <div class="products-grid">
            @forelse($books as $book)
                <div class="product">

                    @if($book->image)
                     <a href="{{ route('books.show', $book->id) }}">
                        <img src="{{ $book->image }}" alt="{{ $book->name }}" class="book-image">
                         
                    </a>
                    @endif

                    <div class="product-header">
                            <h3>{{ $book->name }}</h3>
                        @if($book->is_premium)
                            <span style="font-size: 11px; font-weight: 700; color: #b45309; background: #fef3c7; padding: 3px 8px; border-radius: 999px;">
                                Premium
                            </span>
                        @endif
                        <button type="button" class="wishlist-btn" data-id="{{ $book->id }}">
                            ❤️
                        </button>
                        <!-- <p>₹{{ $book->price }}</p> -->
                    </div>

                  <div>
                
                  </div>
              
                </div>
            @empty
                <p>No books found.</p>
            @endforelse
        </div>


        <div class="pagination">
            {{ $books->links() }}
        </div>

    </div>
</div>

@include('common.footer')


</body>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.wishlist-btn').forEach(button => {

        button.addEventListener('click', function () {
            console.log('❤️ Clicked');

            const bookId = this.dataset.id;
            console.log('Book ID:', bookId);

            fetch("{{ route('wishlist.toggle') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    book_id: bookId
                })
            })
            .then(res => {
                console.log('Response status:', res.status);
                return res.json();
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.status === 'added') {
                    alert('❤️ Added to wishlist');
                } else if (data.status === 'removed') {
                    alert('❌ Removed from wishlist');
                }
            })
            .catch(err => console.error('Error:', err));
        });

    });

});
</script>
</html>
