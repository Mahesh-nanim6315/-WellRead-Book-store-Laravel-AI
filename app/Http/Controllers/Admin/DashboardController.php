<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Book;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===============================
        // 📊 Stats Cards
        // ===============================

        $totalOrders = Order::count();

        // Revenue only from successful payments
        $totalRevenue = Order::where('payment_status', 'paid')
                                ->sum('total_amount');

        $totalUsers = User::count();
        $totalBooks = Book::count();

        // 🆕 Get last 5 orders
         $recentOrders = Order::with('user')
        ->latest()
        ->take(5)
        ->get();

         // 🔴 LOW STOCK (stock less than 5)
         $lowStockBooks = Book::where('has_paperback', true)
        ->where('stock', '<', 5)
        ->orderBy('stock', 'asc')
        ->take(5)
        ->get();

        // 🏆 TOP SELLING BOOKS
         $topSellingBooks = OrderItem::select(
            'book_id',
            DB::raw('SUM(quantity) as total_sold')
        )
        ->groupBy('book_id')
        ->orderByDesc('total_sold')
        ->with('book')
        ->take(5)
        ->get();


        // ===============================
        // 📈 Monthly Sales Chart Data
        // ===============================

        $monthlySales = Order::select(
                DB::raw("MONTH(created_at) as month"),
                DB::raw("SUM(total_amount) as total")
            )
            ->where('payment_status', 'paid')
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->orderBy(DB::raw("MONTH(created_at)"))
            ->get();

        // Format for Chart.js
        $months = [];
        $sales = [];

        foreach ($monthlySales as $data) {
            $months[] = date("F", mktime(0, 0, 0, $data->month, 1));
            $sales[] = $data->total;
        }

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalUsers',
            'totalBooks',
            'months',
            'sales',
            'recentOrders',
            'lowStockBooks',
            'topSellingBooks'
        ));
    }


        public function dashboard()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return view('admin.dashboard-admin');
        }

        if ($user->role === 'manager') {
            return view('admin.dashboard-manager');
        }

        if ($user->role === 'staff') {
            return view('admin.dashboard-staff');
        }

        abort(403);
    }

}
