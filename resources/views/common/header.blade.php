<header>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        $currentLocale = app()->getLocale();
        $cartCount = 0;
        if (auth()->check()) {
            $cart = \App\Models\Cart::with('items')
                ->where('user_id', auth()->id())
                ->first();
            if ($cart) {
                $cartCount = $cart->items->sum('quantity');
            }
        }
    @endphp
    <nav>
        <div class="pair">
            <img src="{{ asset('images/booklogo.png') }}" width="70px" height="40px"/>
            <form action="{{ route('products.home') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="{{ __('messages.search_placeholder') }}" class="search" id="searchInput"/>
                <button type="submit" class="search-btn" aria-label="Search"><i class="fas fa-search"></i></button>
            </form>
            <button class="hamburger" id="hamburger" aria-label="Toggle Menu">&#9776;</button>
        </div>

        <ul id="navMenu">
            <li>
                <a href="{{ url('/') }}">
                    {{ __('messages.home') }}
                </a>
            </li>

            <li>
                <a href="{{ route('ebooks.index') }}">
                    {{ __('messages.ebooks') }}
                </a>
            </li>

            <li>
                <a href="{{ route('audiobooks.index') }}">
                    {{ __('messages.audiobooks') }}
                </a>
            </li>

            <li>
                <a href="{{ route('paperbacks.index') }}">
                    {{ __('messages.paperback_books') }}
                </a>
            </li>

            <li>
                <a href="{{ route('products.home') }}">{{ __('messages.top_books') }}</a>
            </li>

            <li>
                <a href="{{ route('authors.index') }}">
                    {{ __('messages.authors') }}
                </a>
            </li>

            <li>
                <a href="{{ route('library.index') }}">
                    {{ __('messages.my_library') }}
                </a>
            </li>
        </ul>

        <select class="lang-switcher" onchange="window.location.href=this.value">
            <option value="">{{ __('messages.language') }}</option>
            <option value="{{ route('lang.switch', 'en') }}" @selected($currentLocale === 'en')>English</option>
            <option value="{{ route('lang.switch', 'hi') }}" @selected($currentLocale === 'hi')>हिन्दी</option>
            <option value="{{ route('lang.switch', 'ta') }}" @selected($currentLocale === 'ta')>தமிழ்</option>
            <option value="{{ route('lang.switch', 'te') }}" @selected($currentLocale === 'te')>తెలుగు</option>
        </select>

        <div class="parent">
            <div class="child">
                <a href="{{ route('cart.view') }}" class="cart-link">
                    <div class="cart-wrapper">
                     
                        <img src="{{ asset('images/shopping-cart.png') }}" width="45" height="45" class="cart-icon">
                        @if($cartCount > 0)
                            <span class="cart-count-badge" style="background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: absolute; top: 7px; right: 93px;">{{ $cartCount }}</span>
                        @endif
                    </div>
                </a>
            </div>

            <div class="child login-icon">
                @auth
                    <div class="user-dropdown">
                        <button class="user-icon-btn">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <div class="user-dropdown-menu">
                            <a href="{{ route('wishlist.index') }}" class="dropdown-item">
                                <i class="fas fa-heart"></i> {{ __('messages.my_wishlist') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fas fa-user"></i> {{ __('messages.my_profile') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('plans.index') }}">
                                <i class="fas fa-user"></i> {{ __('messages.subscriptions') }}
                            </a>
                            <a href="{{ route('orders.index') }}" class="dropdown-item">
                                <i class="fas fa-shopping-bag"></i> {{ __('messages.my_paperback_orders') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-item-form">
                                @csrf
                                <button type="submit" class="dropdown-item logout-btn">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}">
                        <button class="login-btn">{{ __('messages.login') }}</button>
                    </a>
                @endauth
            </div>
        </div>
    </nav>
</header>
