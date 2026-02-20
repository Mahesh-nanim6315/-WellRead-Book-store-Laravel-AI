<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    @vite(['resources/css/app.css'])

    <style>
        .success-wrapper {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 100px;
        }

        .success-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .success-icon {
            font-size: 60px;
            color: #22c55e;
            margin-bottom: 10px;
        }

        .success-box h2 {
            margin-bottom: 10px;
        }

        .success-box p {
            color: #555;
            margin-bottom: 20px;
        }

        .order-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            text-align: left;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .order-info div {
            margin-bottom: 6px;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>

@include('common.header')

<div class="success-wrapper">
    <div class="success-box">

        <div class="success-icon">✔</div>

        <h2>Order Placed Successfully</h2>
        <p>Your order has been confirmed.</p>

        <div class="order-info">
            <div><strong>Order ID:</strong> {{ $order->id }}</div>
            <div><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</div>
            <div><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</div>
            <div><strong>Order Status:</strong> {{ ucfirst($order->status) }}</div>

            @if(!empty($order->coupon_code))
                <div><strong>Coupon:</strong> {{ $order->coupon_code }}</div>
            @endif
        </div>


        <div class="btn-group">
            <a href="{{ route('home') }}" class="btn-primary">
                Continue Shopping
            </a>

            <a href="{{ route('orders.show', $order->id) }}" class="btn-secondary">
                View Order
            </a>
        </div>

    </div>
</div>

@include('common.footer')

</body>
</html>

