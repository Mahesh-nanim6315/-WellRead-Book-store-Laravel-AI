<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderControllers extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;

        $query = Order::with('user')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

public function show($id)
{
    $order = Order::with(['user', 'items.book', 'address'])
                  ->findOrFail($id);

    return view('admin.orders.show', compact('order'));
}


    // Update order status
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required',
            'payment_status' => 'required',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Order updated successfully');
    }

    public function payments()
    {
        $payments = Order::whereNotNull('payment_id')
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    public function exportCsv()
{
    $orders = Order::with('user')->get();

    $filename = "orders_export_" . now()->format('Y_m_d_H_i_s') . ".csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function () use ($orders) {
        $file = fopen('php://output', 'w');

        // CSV Header row
        fputcsv($file, [
            'Order ID',
            'Customer',
            'Total Amount',
            'Payment Method',
            'Payment Status',
            'Order Status',
            'Date'
        ]);

        foreach ($orders as $order) {
            fputcsv($file, [
                $order->id,
                $order->user->name,
                $order->total_amount,
                $order->payment_method,
                $order->payment_status,
                $order->status,
                $order->created_at,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Order status updated successfully');
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $order = Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();

        return back()->with('success', 'Payment status updated successfully');
    }



}
