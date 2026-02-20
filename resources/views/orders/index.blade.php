@include('common.header')
<div class="orders-page">

    <h2 class="page-title">📦 My Orders</h2>

    @if($orders->count())

        <div class="orders-list">
            @foreach($orders as $order)
                <div class="order-card">

                    <div class="order-header">
                        <div>
                            <strong>Order #{{ $order->id }}</strong>
                            <p class="order-date">
                                {{ $order->created_at->format('d M Y') }}
                            </p>
                        </div>

                        <span class="order-status {{ $order->payment_status }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>

                    <div class="order-body">
                        <p>
                            <strong>Total:</strong> ₹{{ number_format($order->total_amount, 2) }}
                        </p>

                        <p>
                            <strong>Payment:</strong> {{ ucfirst($order->payment_method) }}
                        </p>

                        <p>
                            <strong>Status:</strong> {{ ucfirst($order->status) }}
                        </p>
                    </div>

                    <div class="order-footer">
                        <a href="{{ route('orders.success', $order->id) }}" class="btnes view-btns">
                            View Details
                        </a>

                        <a href="{{ route('orders.invoice', $order->id) }}" class="btnes invoice-btn">
                            Download Invoice
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    @else
        <div class="empty-orders">
            <p>You haven’t placed any orders yet 📚</p>
            <a href="{{ route('home') }}" class="btnes shop-btns">Browse Books</a>
        </div>
    @endif

</div>
@include('common.footer')
