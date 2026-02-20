<?php

namespace App\Http\Controllers;

use App\Events\PaymentSuccess;
use App\Events\OrderPlaced;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserLibrary;
use App\Models\OrderItem;

class StripeController extends Controller
{
   public function checkout(Order $order)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $lineItems = [[
        'price_data' => [
            'currency' => 'inr',
            'product_data' => [
                'name' => 'Book Purchase',
                'description' => 'Final amount after coupon & tax',
            ],
            'unit_amount' => $order->total_amount * 100,
        ],
        'quantity' => 1,
    ]];

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => route('stripe.success', $order->id) . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('cart.view'),
    ]);

    return redirect($session->url);
}


    public function success(Order $order)
    {
        $wasPaid = $order->payment_status === 'paid';
        $sessionId = request('session_id');
        $paymentId = $sessionId;

        if ($sessionId) {
            Stripe::setApiKey(config('services.stripe.secret'));
            try {
                $session = Session::retrieve($sessionId);
                $paymentIntentId = $session->payment_intent ?? null;
                if ($paymentIntentId) {
                    $paymentId = $paymentIntentId;
                }
            } catch (\Exception $e) {
                // fallback to session id if retrieval fails
                $paymentId = $sessionId;
            }
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'payment_id' => $paymentId
        ]);

        // Add books to user library
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        foreach ($orderItems as $item) {
            UserLibrary::firstOrCreate(
                [
                    'user_id' => $order->user_id,
                    'book_id' => $item->book_id,
                    'format'  => $item->format,
                ],
                [
                    'expires_at' => in_array($item->format, ['ebook','audio'])
                        ? now()->addDays(30)
                        : null
                ]
            );
        }

        if (! $wasPaid) {
            event(new PaymentSuccess($order->fresh('user')));
            event(new OrderPlaced($order->fresh('user')));
        }

        return redirect()->route('orders.success', $order->id);
    }
}

