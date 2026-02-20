<!DOCTYPE html>
<html>
<head>
    <title>Fantasy Collection</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@include('common.header')

<h1 style="text-align:center; margin-top:100px;">
    🧙‍♂️ Comic Collection
</h1>

<div class="products-grid">
    @foreach($books as $book)
        <div class="product">

            <img src="{{ $book->image }}" width="148" height="148">

            <h3>{{ $book->name }}</h3>
            
                 <div style="display: flex; align-items: center;">
                   
                   <strong>₹{{ $book->price }}</strong>
                        <button class="wishlist-btn"
                                data-id="{{ $book->id }}"
                                style="border-radius:50%; margin-left:5px;">
                               ❤️
                        </button>
                </div>

            <br><br>

            <a href="{{ route('product.details', $book->id) }}">
                <img src="/images/view.png" width="20">
            </a>

                   <a href="{{ route('books.edit', $book->id) }}">
                        <img src="images/edit.png" width="20">
                    </a>

                    <form action="{{ route('books.destroy', $book->id) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button style="background:none;border:none;padding:0;"
                                onclick="return confirm('Delete this book?')">
                            <img src="images/delete.png" width="20">
                        </button>
                    </form>

        </div>
    @endforeach
</div>

   <!-- Pagination -->
        <div style="margin:40px 0; display:flex; padding:10px; justify-content:center; background:#F075AE; color:black; border-radius:6px;">
            {{ $books->links() }}
        </div>

@include('common.footer')

</body>

<script>
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            fetch("{{ route('wishlist.toggle') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    book_id: this.dataset.id
                })
            })
            .then(res => res.json())
            .then(data => {
                this.textContent = data.status === 'added' ? '💖' : '❤️';
            })
            .catch(err => console.error(err));
        });
    });
</script>

</html>
