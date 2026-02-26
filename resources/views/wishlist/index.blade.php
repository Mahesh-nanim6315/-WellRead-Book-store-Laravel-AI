@include('common.header')

<div class="wishlist-container" id="wishlist-section">
    <h2 class="wishlist-title" id="wishlist-heading">❤️ My Wishlist</h2>

    @if($wishlists->count())
        <div class="wishlist-grid" id="wishlist-grid">
            @foreach($wishlists as $item)
                <div class="wishlist-item" id="wishlist-item-{{ $item->id }}">
                    <img src="{{ $item->book->image }}" 
                         alt="{{ $item->book->name }}" 
                         class="wishlist-image" 
                         width="200" height="300">

                    <h4 class="wishlist-book-title">{{ $item->book->name }}</h4>
                    
                    <div class="wishlist-actions" id="wishlist-actions-{{ $item->id }}">
                        @auth
                        <form action="{{ route('cart.add', $item->book->id) }}" method="POST" class="wishlist-form">
                            @csrf
                            <input type="hidden" name="format" value="ebook">
                            <input type="hidden" name="price" value="{{ $item->book->price }}">
                            <button type="submit" class="wishlist-btn add-btn">🛒 Add</button>
                        </form>
                        @else
                            <a href="{{ route('login') }}" class="wishlist-btn login-btn">
                                Login to Add
                            </a>
                        @endauth

                        <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="wishlist-form">
                            @csrf
                            @method('DELETE')
                            <button class="wishlist-btn remove-btn" type="submit">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="wishlist-empty">Your wishlist is empty.</p>
    @endif
</div>

@include('common.footer')
