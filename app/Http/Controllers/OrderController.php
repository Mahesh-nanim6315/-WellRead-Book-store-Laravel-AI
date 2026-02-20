<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\UserLibrary;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /* ================= PLACE ORDER ================= */
    public function store(Request $request)
    {
        $user = Auth::user();

        $cart = Cart::with('items')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.view')->with('error', 'Cart is empty');
        }

        // CALCULATE TOTAL
        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // CREATE ORDER
        $order = Order::create([
            'user_id'        => $user->id,
            'total_amount'   => $total,
            'status'         => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'online',
            'payment_id'     => $request->payment_id ?? null,
        ]);

        event(new OrderPlaced($order)); 

        /* ================= ORDER ITEMS ================= */
        foreach ($cart->items as $item) {

            OrderItem::create([
                'order_id' => $order->id,
                'book_id'  => $item->book_id,
                'format'   => $item->format,
                'price'    => $item->price,
                'quantity' => $item->quantity,
            ]);

            /* ================= USER LIBRARY ================= */
            UserLibrary::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'book_id' => $item->book_id,
                    'format'  => $item->format,
                ],
                [
                    'expires_at' => in_array($item->format, ['ebook','audio'])
                        ? now()->addDays(30)
                        : null,
                ]
            );
        }

        // CLEAR CART
        $cart->items()->delete();
        $cart->delete();

        return redirect()->route('orders.success', $order->id);
    }

    /* ================= ORDER SUCCESS ================= */
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.book');

        return view('orders.success', compact('order'));
    }

    /* ================= MY ORDERS ================= */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    /* ================= INVOICE ================= */
    public function downloadInvoice(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.book');

        $pdf = Pdf::loadView('invoices.invoice', compact('order'));

        return $pdf->download('invoice-order-' . $order->id . '.pdf');
    }

      public function address(Book $book)
    {
        return view('checkout.address', compact('book'));
    }

        public function show(Order $order)
    {
        $order->load('items.book');

        return view('orders.show', compact('order'));
    }

}
