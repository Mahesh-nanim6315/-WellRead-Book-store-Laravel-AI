<?php

namespace App\Http\Controllers;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderPlaced;
use App\Events\PaymentSuccess;

class PayPalController extends Controller
{
 

public function pay(Order $order)
{
    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $response = $provider->createOrder([
        "intent" => "CAPTURE",
        "application_context" => [
            "return_url" => route('paypal.success', $order->id),
            "cancel_url" => route('paypal.cancel', $order->id),
        ],
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value" => number_format($order->total_amount, 2, '.', '')
                ]
            ]
        ]
    ]);

    if (isset($response['links'])) {
        foreach ($response['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return redirect()->away($link['href']);
            }
        }
    }

    return back()->with('error', 'Unable to initiate PayPal payment');
}




//     public function success(Request $request, Order $order)
// {
//     $provider = new PayPalClient;
//     $provider->setApiCredentials(config('paypal'));
//     $provider->getAccessToken();

//     $response = $provider->capturePaymentOrder($request->token);

//     if ($response['status'] === 'COMPLETED') {

//         $order->update([
//             'payment_method' => 'paypal',
//             'payment_id' => $response['id'],
//             'payment_status' => 'paid',
//             'status' => 'completed',
//         ]);

//         return redirect()->route('orders.success', $order->id)
//             ->with('success', 'Payment successful via PayPal');
//     }

//     return redirect()->route('payment.page', $order->id)
//         ->with('error', 'Payment failed');
// }

public function success(Request $request, Order $order)
{
    $wasPaid = $order->payment_status === 'paid';

    $provider = new PayPalClient;
    $provider->setApiCredentials(config('paypal'));
    $provider->getAccessToken();

    $response = $provider->capturePaymentOrder($request->token);

    if (isset($response['status']) && $response['status'] === 'COMPLETED') {
        $order->update([
            'payment_id' => $response['id'],
            'payment_status' => 'paid',
            'status' => 'completed'
        ]);

        if (! $wasPaid) {
            event(new PaymentSuccess($order->fresh('user')));
            event(new OrderPlaced($order->fresh('user')));
        }

        return redirect()->route('orders.success', $order->id);
    }

    return redirect()->route('payment.page', $order->id)
        ->with('error', 'Payment not completed');
}


// public function cancel(Order $order)
// {
//     return redirect()->route('payment.page', $order->id)
//         ->with('error', 'Payment cancelled');
// }

public function cancel(Order $order)
{
    return redirect()->route('payment.page', $order->id)
        ->with('error', 'Payment cancelled');
}



}
