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
            @if($book->is_premium)
                <span style="font-size: 11px; font-weight: 700; color: #b45309; background: #fef3c7; padding: 3px 8px; border-radius: 999px;">
                    Premium
                </span>
            @endif
        </div>
    @empty
        <p class="no-books">No books found.</p>
    @endforelse
</div>

<div class="pagination">
    {{ $books->links() }}
</div>

@include('common.footer')
