
<header>
           <meta name="viewport" content="width=device-width, initial-scale=1.0">
            @vite(['resources/css/app.css', 'resources/js/app.js'])
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <nav>
        <div class="pair">
            <img src="{{asset('images/booklogo.png')}}" width="70px" height="40px"/>
            <form action="{{ route('products.home') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="search here" class="search" id="searchInput"/>
                <button type="submit" class="search-btn" aria-label="Search"><i class="fas fa-search"></i></button>
            </form>
            <button class="hamburger" id="hamburger" aria-label="Toggle Menu">&#9776;</button>
       </div>
     
    <ul id="navMenu">

      <li>
        <a href="{{ url('/') }}">
           Home
        </a>
      </li>

        <li>
        <a href="{{ route('ebooks.index') }}">
            E-Books
        </a>
      </li>

        <li>
        <a href="{{ route('audiobooks.index') }}">
            Audio Books
        </a>
      </li>

        <li>
        <a href="{{ route('paperbacks.index') }}">
            Paperback Books
        </a>
      </li>

    
      <li>
       <a href="{{ route('products.home') }}">Top Books</a>

      </li>

      <li>
        <a href="{{ route('authors.index') }}">
            Authors
        </a>
      </li>

        <li>
            <a href="{{ route('library.index') }}">
                My Library
            </a>
        </li>
    </ul>


<select class="lang-switcher" onchange="window.location.href=this.value">
    <option value="">🌐 Language</option>
    <option value="{{ route('lang.switch', 'en') }}">English</option>
    <option value="{{ route('lang.switch', 'hi') }}">हिन्दी</option>
    <option value="{{ route('lang.switch', 'ta') }}">தமிழ்</option>
    <option value="{{ route('lang.switch', 'te') }}">తెలుగు</option>
</select>



    <div class="parent">

    <div class="child">

    <!-- <span class="cart-count">
        {{ auth()->check() && auth()->user()->cart?->items->count() ?? 0 }}
    </span>  -->

      <a href="{{ route('cart.view') }}">
              <img src="{{ asset('images/shopping-cart.png') }}" width="45" height="45" class="cart-icon">
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
                    <i class="fas fa-heart"></i> My Wishlist
                </a>
                <a class="dropdown-item" href="{{ route('profile') }}">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <a class="dropdown-item" href="{{ route('plans.index') }}">
                    <i class="fas fa-user"></i> subscriptions
                </a>
                <a href="{{ route('orders.index') }}" class="dropdown-item">
                    <i class="fas fa-shopping-bag"></i> My Paperback Orders
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" class="dropdown-item-form">
                    @csrf
                    <button type="submit" class="dropdown-item logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}">
            <button class="login-btn">Login</button>
        </a>
    @endauth
</div>


      
    </div>
     
  </nav>
</header>







