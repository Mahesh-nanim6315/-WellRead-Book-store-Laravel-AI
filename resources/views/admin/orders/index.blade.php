@extends('admin.layouts.app')

@section('content')

<h2>Orders Management</h2>

<div class="page-header">
    <div style="margin-bottom:15px;">
        <a href="{{ route('admin.orders.index') }}">All</a> |
        <a href="?status=pending">Pending</a> |
        <a href="?status=delivered">Delivered</a> |
        <a href="?status=cancelled">Cancelled</a>
    </div>

    <div style="margin-bottom:15px;">
        <a href="{{ route('admin.orders.export') }}" 
        style="padding:8px 15px;background:#28a745;color:white;text-decoration:none;border-radius:5px;">
        Export CSV
        </a>
    </div>
</div>



<table border="1" cellpadding="10" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>#{{ $order->id }}</td>
            <td>{{ $order->user->name }}</td>
            <td>₹{{ $order->total_amount }}</td>
           <td>
                <form action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <select name="payment_status" onchange="this.form.submit()" class="status-dropdown">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </form>
            </td>

            <td>

                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <select name="status" onchange="this.form.submit()" class="status-dropdown">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </form>

            </td>

            <td>{{ $order->created_at->format('d M Y') }}</td>
            <td>
                <a href="{{ route('admin.orders.show', $order->id) }}">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:15px;">
    {{ $orders->links() }}
</div>

@endsection
