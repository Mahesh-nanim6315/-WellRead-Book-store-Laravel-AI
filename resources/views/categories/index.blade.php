@include('common.header')

@section('content')

<div class="categories-page" style="margin-top: 15px;">

    <div class="categories-header">
        <h1>All Categories</h1>
        <p>Explore books by category</p>
    </div>

    <div class="categories-grid">
        @foreach($categories as $category)
            <a href="{{ route('category.books', $category->slug) }}" 
               class="category-card">

                <div class="category-name">
                    {{ $category->name }}
                </div>

                <div class="category-count">
                    {{ $category->books_count }} Books
                </div>
                <div class="category-count">
                    eBook: {{ $category->ebooks_count }}
                </div>
                <div class="category-count">
                    Audiobook: {{ $category->audiobooks_count }}
                </div>
                <div class="category-count">
                    Paperback: {{ $category->paperbacks_count }}
                </div>

            </a>
        @endforeach
    </div>

</div>

@include('common.footer')
