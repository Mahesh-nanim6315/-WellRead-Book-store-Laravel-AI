<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    @vite(['resources/css/app.css'])
</head>
<body>


@include('common.header')

<div style="margin-top:100px; padding:30px; max-width:1200px; margin-left:auto; margin-right:auto;">

    <h2 style="margin-bottom:20px;">🛒 Your Shopping Cart</h2>
   
     @if($cart && $cart->items->count() > 0)

    <div style="display:flex; gap:30px;">

        <!-- LEFT: CART ITEMS -->
        <div style="flex:2; background:#fff; padding:20px; border-radius:8px;">

            @foreach($cart->items as $item)
            <div style="display:flex; gap:20px; padding:15px 0; border-bottom:1px solid #eee;">

           
            <img src="{{ $item->book->image }}"
                 width="100" height="130"
                 style="object-fit:cover;">

            <div style="flex:1;">
                <h4>{{ $item->book->name }}</h4>
                <p>Format: {{ ucfirst($item->format) }}</p>
                <p>Price: ₹{{ $item->price }}</p>
                
                <div style="display:flex; align-items:center; gap:10px;">

                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="decrease">
                        <button style="padding:4px 10px;">−</button>
                    </form>

                    <strong>{{ $item->quantity }}</strong>

                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="increase">
                        <button style="padding:4px 10px;">+</button>
                    </form>

                </div>

                </div>

                <div>
                    <strong>
                        ₹{{ $item->price * $item->quantity }}
                    </strong>
                </div>

                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button style="color:red; background:none; border:none; cursor:pointer";>
                        Remove
                    </button>
                </form>

            </div>
            @endforeach

        </div>

        <!-- RIGHT: SUMMARY -->
        <div style="flex:1; background:#f9f9f9; padding:20px; border-radius:8px; height:fit-content;">

       @php
            $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);

            $discount = session('coupon.discount', 0);

            $tax = round(($subtotal - $discount) * 0.05);
            $total = max(0, $subtotal - $discount + $tax);
        @endphp

            <h3>{{ __('messages.order_summary') }}</h3>

            <p>Subtotal: ₹{{ $subtotal }}</p>
            <p>Tax (5%): ₹{{ $tax }}</p>

            <hr>

            <h3>Total: ₹{{ $total }}</h3>

            <a href="{{ route('checkout.index') }}">
                <button style="width:100%; padding:12px; background:#ddba1d; border:none; cursor:pointer">
                    Proceed to Checkout
                </button>
            </a>


                @if(session('coupon'))
            <p style="color:green; margin: bottom 10px;">
                Coupon <strong>{{ session('coupon.code') }}</strong> applied
                ({{ session('coupon.discount') }}% OFF)
            </p>

             <form action="{{ route('cart.coupon.remove') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button style="background:none; color:red; border:none;">
                        Remove coupon
                    </button>
                </form>
            @else
                <form action="{{ route('cart.coupon') }}" method="POST" style="margin-bottom:15px;">
                    @csrf
                    <input type="text" name="code" placeholder="Enter coupon"
                        style="width:100%; padding:8px; margin-bottom:8px;">
                    <button style="width:100%; padding:8px;">
                        Apply Coupon
                    </button>
                </form>
            @endif
        </div>
    </div>

     
    @else
        <div style="text-align:center; padding:60px;">
            <h3>Your cart is empty 😔</h3>
            <a href="{{ route('home') }}">
                <button style="margin-top:20px; padding:10px 20px;">
                    Continue Shopping
                </button>
            </a>
        </div>
    @endif

</div>

@include('common.footer')

</body>
</html>
