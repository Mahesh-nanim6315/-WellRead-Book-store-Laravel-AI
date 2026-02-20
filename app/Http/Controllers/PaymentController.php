<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
public function process(Request $request, Order $order)
{
    $request->validate([
        'payment_method' => 'required'
    ]);

    // Save selected method
    $order->update([
        'payment_method' => $request->payment_method
    ]);

    switch ($request->payment_method) {

        case 'stripe':
            return redirect()->route('stripe.checkout', $order->id);

        case 'paypal':
            return redirect()->route('paypal.pay', $order->id);

        case 'cod':
            $order->update([
                'payment_status' => 'pending',
                'status' => 'placed'
            ]);
            return redirect()->route('orders.success', $order->id);

        default:
            return back()->with('error', 'Invalid payment method');
    }
}

}
