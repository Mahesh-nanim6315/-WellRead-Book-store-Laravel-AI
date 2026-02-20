@extends('admin.layout')

@section('content')

<h2>Order Details #{{ $order->id }}</h2>

<div class="card">
    <h4>Customer Information</h4>
    <p><strong>Name:</strong> {{ $order->user->name }}</p>
    <p><strong>Email:</strong> {{ $order->user->email }}</p>
</div>

<div class="card">
    <h4>Shipping Address</h4>
    @if($order->address)
        <p>{{ $order->address->full_name }}</p>
        <p>{{ $order->address->address_line }}</p>
        <p>{{ $order->address->city }}, {{ $order->address->state }}</p>
        <p>{{ $order->address->pincode }}</p>
    @else
        <p>Digital Order (No Shipping Required)</p>
    @endif
</div>

<div class="card">
    <h4>Order Summary</h4>
    <p><strong>Status:</strong> {{ $order->status }}</p>
    <p><strong>Payment Status:</strong> {{ $order->payment_status }}</p>
    <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
    <p><strong>Subtotal:</strong> ₹{{ $order->subtotal }}</p>
    <p><strong>Tax:</strong> ₹{{ $order->tax_amount }}</p>
    <p><strong>Discount:</strong> ₹{{ $order->discount_amount }}</p>
    <p><strong>Total:</strong> ₹{{ $order->total_amount }}</p>
</div>

<div class="card">
    <h4>Ordered Items</h4>
    <table border="1" width="100%" cellpadding="10">
        <tr>
            <th>Book</th>
            <th>Format</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>

        @foreach($order->items as $item)
        <tr>
            <td>{{ $item->book->title }}</td>
            <td>{{ $item->format }}</td>
            <td>{{ $item->quantity }}</td>
            <td>₹{{ $item->price }}</td>
        </tr>
        @endforeach
    </table>
</div>

@endsection
