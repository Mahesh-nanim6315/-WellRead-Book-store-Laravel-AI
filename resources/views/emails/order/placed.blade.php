@component('mail::message')

# Order Placed

Hi {{ $order->user->name }},

Your order **#{{ $order->id }}** has been placed successfully.

**Total:** Rs {{ number_format($order->total_amount, 2) }}

@component('mail::button', ['url' => url('/orders/'.$order->id)])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent
