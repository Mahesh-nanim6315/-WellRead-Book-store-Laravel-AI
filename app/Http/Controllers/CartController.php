<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Book;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    /* ================= ADD TO CART ================= */
   
    public function add(Request $request, Book $book)
    {
         
        $request->validate([
            'format' => 'required|in:ebook,audio,paperback',
            'price'  => 'required|numeric|min:0',
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('book_id', $book->id)
            ->where('format', $request->format)
            ->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'book_id' => $book->id,
                'format'  => $request->format,
                'price'   => $request->price,
                'quantity'=> 1,
            ]);
        }
      
        return back()->with(
            'success',
            ucfirst($request->format) . ' added to cart'
        );
    }


    /* ================= VIEW CART ================= */
    public function view()
    {
        $cart = Cart::with('items.book')
            ->where('user_id', Auth::id())
            ->first();

        return view('cart.index', compact('cart'));
    }

    /* ================= REMOVE ITEM ================= */
    public function remove(CartItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item removed');
    }

    /* ================= UPDATE QTY ================= */
   public function update(Request $request, CartItem $item)
{
    if ($request->action === 'increase') {
        $item->increment('quantity');
    }

    if ($request->action === 'decrease') {
        $item->decrement('quantity');
    }

    if ($item->quantity <= 0) {
        $item->delete();
    }

    return back();
}

public function applyCoupon(Request $request)
{
    $request->validate([
        'code' => 'required|string'
    ]);

    $code = strtoupper($request->code);

    // Match SAVE50, SAVE20, FLAT100 etc
    if (!preg_match('/^(SAVE|FLAT)(\d+)$/', $code, $matches)) {
        return back()->with('error', 'Invalid coupon code');
    }

    $prefix = $matches[1]; // SAVE or FLAT
    $value  = (int) $matches[2];

    $rules = config("coupons.$prefix");

    if (!$rules || !in_array($value, $rules['allowed_values'])) {
        return back()->with('error', 'Invalid coupon value');
    }

    $cart = Cart::with('items')->where('user_id', Auth::id())->first();
    $subtotal = $cart->items->sum(fn($i) => $i->price * $i->quantity);

    if ($subtotal <= 0) {
        return back()->with('error', 'Cart is empty');
    }

    if ($rules['type'] === 'percentage') {
        $discount = ($subtotal * $value) / 100;

        // apply max cap if exists
        if (isset($rules['max_discount'])) {
            $discount = min($discount, $rules['max_discount']);
        }
    } else {
        $discount = $value;
    }

    session([
        'coupon' => [
            'code' => $code,
            'type' => $rules['type'],
            'value' => $value,
            'discount' => round($discount),
        ]
    ]);

    return back()->with('success', "Coupon {$code} applied successfully");
}



public function removeCoupon()
{
    session()->forget('coupon');
    return back()->with('success', 'Coupon removed');
}


}
