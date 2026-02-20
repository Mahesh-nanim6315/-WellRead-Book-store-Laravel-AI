@extends('admin.layouts.app')

@section('content')

<h2>Payment Logs</h2>

<table border="1" cellpadding="10" width="100%">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Payment ID</th>
            <th>Method</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td>#{{ $payment->id }}</td>
            <td>{{ $payment->user->name }}</td>
            <td>{{ $payment->payment_id }}</td>
            <td>{{ ucfirst($payment->payment_method) }}</td>
            <td>₹{{ $payment->total_amount }}</td>
            <td>{{ ucfirst($payment->payment_status) }}</td>
            <td>{{ $payment->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $payments->links() }}

@endsection
