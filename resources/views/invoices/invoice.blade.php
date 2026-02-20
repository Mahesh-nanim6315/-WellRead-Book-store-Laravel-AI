<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
        }

        .invoice-box {
            width: 100%;
            padding: 20px;
        }

        h2 {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background: #f4f4f4;
        }

        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <h2>📚 Book Store Invoice</h2>
    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
    <p><strong>Customer:</strong> {{ auth()->user()->name }}</p>

    <table>
        <thead>
            <tr>
                <th>Book</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->book->name }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">
        Grand Total: ₹{{ number_format($order->total_amount, 2) }}
    </p>
</div>

</body>
</html>
