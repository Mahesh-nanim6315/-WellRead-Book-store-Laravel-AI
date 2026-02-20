@include('common.header')

<div class="order-container">

    <!-- Order Header -->
    <div class="order-header">
        <h2>Order Details</h2>
        <span class="order-status">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <!-- Order Info -->
    <div class="order-info">
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p>
        <strong>Placed On:</strong>
        {{ $order->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
        </p>
        <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
        <p><strong>Payment Method:</strong> {{ $order->payment_method ?? 'Online' }}</p>
    </div>

    <!-- Items -->
    <h3 class="section-title">Books Ordered</h3>

    <table class="order-table">
        <thead>
            <tr>
                <th>Book</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->book->name }}</strong><br>
                        <small>{{ $item->book->author->name ?? '' }}</small>
                    </td>
                    <td>₹{{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <!-- Order Summary -->
    <div class="order-summary">
        <h3>Total: ₹{{ number_format($order->total_amount, 2) }}</h3>
    </div>

    <!-- Actions -->
    <div class="order-actions">
        <a href="{{ route('home') }}" class="btn btn-secondary">
            Continue Shopping
        </a>
        <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-primary">
            Download Invoice
        </a>



    </div>

</div>

@include('common.footer')

