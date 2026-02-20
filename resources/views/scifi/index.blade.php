<!DOCTYPE html>
<html>
<head>
    <title>Anime Collection</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@include('common.header')

<h1 style="text-align:center; margin-top:100px;">🎌 Sci-Fi Collection</h1>

<div class="products-grid">
    @foreach($books as $book)
        <div class="product">

            <img src="{{ $book->image }}" width="148" height="148">

            <h3>{{ $book->name }}</h3>
            
               <div style="display: flex; align-items: center;">
                   
                   <strong>₹{{ $book->price }}</strong>
                    <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST" style="margin-left: 10px;">
                        @csrf
                        <button type="submit"
                                style="background:none; border:none; cursor:pointer; font-size:22px; ">
                            ❤️
                        </button>
                    </form>
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
</html>