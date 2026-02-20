@extends('admin.layouts.app')

@section('content')
<div class="dashboard">

<div class="stats">
    <div class="card">
        Orders<br>
        <strong>{{ $totalOrders }}</strong>
    </div>

    <div class="card">
        Revenue<br>
        <strong>₹{{ number_format($totalRevenue) }}</strong>
    </div>

    <div class="card">
        Users<br>
        <strong>{{ $totalUsers }}</strong>
    </div>

    <div class="card">
        Books<br>
        <strong>{{ $totalBooks }}</strong>
    </div>
</div>

<hr>

<h3>⚡ Quick Actions</h3>

<div class="quick-actions">
    <a href="{{ route('admin.books.create') }}" class="quick-card">
        ➕ Add Book
    </a>

    <a href="{{ route('admin.authors.create') }}" class="quick-card">
        ➕ Add Author
    </a>

    <a href="{{ route('admin.users.index') }}" class="quick-card">
        👥 Manage Users
    </a>

    <a href="{{ route('admin.orders.index') }}" class="quick-card">
        📦 View Orders
    </a>
</div>

<div class="chart-alerts">
    <div class="chart-box">
        <canvas id="salesChart"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const ctx = document.getElementById('salesChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($months),
                    datasets: [{
                        label: 'Monthly Sales',
                        data: @json($sales),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
</script>
    </div>

    <div class="alerts">
        <h3>⚠ Low Stock Alerts</h3>
        @if($lowStockBooks->count())
            <div class="alert-box">
                @foreach($lowStockBooks as $book)
                    <div class="alert-item">
                        📕 {{ $book->name }}
                        <span class="stock-count">Stock: {{ $book->stock }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p>All books have sufficient stock 👍</p>
        @endif
    </div>
</div>

<hr>

<div class="card-section">
    <h3 class="section-title">🏆 Top Selling Books</h3>

    <div class="card-box">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Total Sold</th>
                </tr>
            </thead>

            <tbody>
                @forelse($topSellingBooks as $item)
                <tr>
                    <td>{{ $item->book->name ?? 'Book Removed' }}</td>
                    <td>
                        <span class="badge-sales">
                            {{ $item->total_sold }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="empty-data">
                        No sales data yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<div>
    <h3>🕒 Recent Orders</h3>

    <table class="table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @forelse($recentOrders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'Guest' }}</td>
                <td>₹{{ $order->total_amount }}</td>
                <td>
                    <span class="status-badge">
                        {{ $order->status ?? 'Pending' }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}">
                        View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No recent orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


</div>
@endsection


