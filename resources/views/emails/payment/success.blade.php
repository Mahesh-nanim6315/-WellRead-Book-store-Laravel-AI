@component('mail::message')

# Payment Successful

Hi {{ $order->user->name }},

Your payment was successful.

Order ID: {{ $order->id }}
Total Amount: Rs {{ number_format($order->total_amount, 2) }}

Thanks for shopping with us,<br>
{{ config('app.name') }}

@endcomponent
