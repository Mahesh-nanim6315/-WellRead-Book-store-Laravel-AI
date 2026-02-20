<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Book;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Show checkout address form
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with('items.book')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.view')
                ->with('error', 'Your cart is empty');
        }

        // Check if cart needs physical delivery
        $needsAddress = $cart->items->contains(function ($item) {
            return strtolower($item->format) === 'paperback';
        });

        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $tax = round($subtotal * 0.05); // 5%

        $coupon = session('coupon');

        $discount = $coupon['discount'] ?? 0;
        $couponCode = $coupon['code'] ?? null;

        $total = max(0, $subtotal + $tax - $discount);
        


        return view('checkout.address', compact('cart', 'subtotal', 'tax', 'total', 'needsAddress', 'discount', 'couponCode'));
    }

    // Process checkout from cart (creates order)
    public function process(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            // Get user's cart with items
            $cart = Cart::with('items.book')->where('user_id', $user->id)->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.view')->with('error', 'Your cart is empty');
            }

            $addressId = null;
            
            // Validate and save address if paperback exists
            if ($cart->items->contains('format', 'paperback')) {
                $request->validate([
                    'full_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'address_line' => 'required|string',
                    'city' => 'required|string|max:100',
                    'state' => 'required|string|max:100',
                    'pincode' => 'required|string|max:10',
                ]);

                // Save address
                $address = Address::create([
                    'user_id' => $user->id,
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'address_line' => $request->address_line,
                    'city' => $request->city,
                    'state' => $request->state,
                    'pincode' => $request->pincode,
                    'country' => $request->country ?? 'India', // Default country
                ]);
                
                $addressId = $address->id;
            }

            // Calculate totals
            $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
            $tax = round($subtotal * 0.05); // 5% tax
          $coupon = session('coupon');

            $discount = $coupon['discount'] ?? 0;
            $couponCode = $coupon['code'] ?? null;

            $total = max(0, $subtotal + $tax - $discount);


           
            // Create order
            $order = Order::create([
                'user_id'         => $user->id,
                'subtotal'        => $subtotal,
                'tax_amount'      => $tax,
                'discount_amount' => $discount,
                'coupon_code'     => $couponCode,
                'total_amount'    => $total,
                'address_id'      => $addressId,
                'status'          => 'pending',
                'payment_status'  => 'pending',
            ]);

           

            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'format' => $item->format,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }

            session()->forget('coupon');
 
          
            // Clear cart
            $cart->items()->delete();

            DB::commit();
            // Redirect to payment page
            return redirect()->route('payment.page', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout failed: ' . $e->getMessage());
            dd($e->getMessage());
        }
    }

    // Show payment page
    public function paymentPage(Order $order)
    {
        // Check if order belongs to current user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        // Check if order is still pending
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'This order has already been processed.');
        }

        return view('checkout.payment', compact('order'));
    }

    // Process payment
    public function processPayment(Request $request, Order $order)
    {
        // Check if order belongs to current user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        $request->validate([
            'payment_method' => 'required|in:stripe,paypal,cod',
        ]);

        $method = $request->payment_method;

        // Update order with payment method
        $order->update([
            'payment_method' => $method,
        ]);

        // Handle different payment methods
        if ($method === 'stripe') {
            return redirect()->route('stripe.checkout', $order->id);
        }

        if ($method === 'paypal') {
            return redirect()->route('paypal.pay', $order->id);
        }

        if ($method === 'cod') {
            $order->update([
                'payment_status' => 'pending',
                'status' => 'placed'
            ]);

            event(new OrderPlaced($order->fresh('user')));

            return redirect()->route('orders.success', $order->id);
        }

        return back()->with('error', 'Invalid payment method selected.');
    }

    // Buy Now functionality
    public function buyNow(Book $book)
    {
        $user = Auth::user();

        // Calculate totals
        $subtotal = $book->price;
        $tax = round($subtotal * 0.05);
        $total = $subtotal + $tax;

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantity' => 1,
            'price' => $book->price,
            'format' => 'paperback', // Default format for buy now
        ]);

        // Redirect to address page for this order
        return redirect()->route('checkout.address.buynow', $order->id);
    }

    // Show address form for buy now
    public function addressBuyNow(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order->id);
        }

        return view('checkout.buynow_address', compact('order'));
    }

    // Store address for buy now order
    public function storeBuyNowAddress(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
        ]);

        // Save address
        $address = Address::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address_line' => $request->address_line,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country ?? 'India',
        ]);

        // Update order with address
        $order->update([
            'address_id' => $address->id,
            ]);

        return redirect()->route('payment.page', $order->id);
    }


    public function success(Order $order)
    {
        // Check if order belongs to current user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $order->load('items.book');
        
        return view('checkout.success', compact('order'));
    }
    }
