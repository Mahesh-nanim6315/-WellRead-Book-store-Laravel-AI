@include('common.header')

<div class="container-con" style="margin-top:100px">
    <h2 id="wh2">❤️ My Wishlist</h2>

    @if($wishlists->count())

        <div class="products-grid">
            @foreach($wishlists as $item)
                <div class="product">
                    <img src="{{ $item->book->image }}" width="250">

                    <h4>{{ $item->book->name }}</h4>
                    <!-- <span class="price">₹{{ $item->book->price }}</span> -->

                    <div style="display:flex; gap:10px; justify-content:center;">
                       
                    @auth
                    <form action="{{ route('cart.add', $item->book->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="format" value="ebook">
                        <input type="hidden" name="price" value="{{ $item->book->price }}">
                        <button type="submit" class="add-to-cart">🛒 Add</button>
                    </form>
                    @else
                        <a href="{{ route('login') }}" class="add-to-cart">
                            Login to Add
                        </a>
                    @endauth
                        <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="remove-from-cart" type="submit">Remove</button>

                        </form>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <p>Your wishlist is empty.</p>
    @endif
</div>

@include('common.footer')
