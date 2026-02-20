@include('common.header')

<h2 class="page-titles">
    {{ $category }} Books
</h2>

<div class="book-grid">
    @forelse($books as $book)
        <div class="book-card">
              <a href="{{ route('books.show', $book->id) }}">
                   <img src="{{ $book->image }}" alt="{{ $book->name }}" class="book-image">
              </a>
            <h4 class="book-title">{{ $book->name }}</h4>
        </div>
    @empty
        <p class="no-books">No books found.</p>
    @endforelse
</div>

<div class="pagination">
    {{ $books->links() }}
</div>

@include('common.footer')
