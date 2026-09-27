@extends("layouts.admin")
@section("title", "Dashboard")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Welcome back, {{ auth()->user()->name }} 👋</h4>
    <span class="text-muted">{{ now()->format("l, d M Y") }}</span>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-primary-soft">
            <div class="stat-icon text-primary"><i class="bi bi-cash-coin"></i></div>
            <div><div class="stat-value">৳{{ number_format($todaySales, 2) }}</div><div class="stat-label">Today's Sales</div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-success-soft">
            <div class="stat-icon text-success"><i class="bi bi-graph-up-arrow"></i></div>
            <div><div class="stat-value">৳{{ number_format($monthProfit, 2) }}</div><div class="stat-label">This Month Profit</div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-warning-soft">
            <div class="stat-icon text-warning"><i class="bi bi-exclamation-triangle"></i></div>
            <div><div class="stat-value">{{ $lowStockCount }}</div><div class="stat-label">Low Stock Items</div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-danger-soft">
            <div class="stat-icon text-danger"><i class="bi bi-wallet2"></i></div>
            <div><div class="stat-value">৳{{ number_format($totalDue, 2) }}</div><div class="stat-label">Total Due (Sales)</div></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-calendar3 text-primary"></i><div><b>৳{{ number_format($monthSales,2) }}</b><span>This Month Sales</span></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-calendar-check text-info"></i><div><b>৳{{ number_format($yearSales,2) }}</b><span>This Year Sales</span></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-truck text-secondary"></i><div><b>৳{{ number_format($monthPurchase,2) }}</b><span>This Month Purchase</span></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-box-seam text-dark"></i><div><b>{{ $totalProducts }}</b><span>Total Products</span></div></div></div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header fw-bold">Sales & Profit Trend (Last 12 Months)</div>
            <div class="card-body"><canvas id="salesTrendChart" height="110"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header fw-bold">Stock Value by Category</div>
            <div class="card-body"><canvas id="categoryStockChart" height="220"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header fw-bold">Sales vs Purchase (Last 7 Days)</div>
            <div class="card-body"><canvas id="salesPurchaseChart" height="150"></canvas></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header fw-bold">Top Selling Products</div>
            <div class="card-body"><canvas id="topProductsChart" height="150"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header fw-bold text-danger"><i class="bi bi-exclamation-circle"></i> Low Stock Alerts</div>
            <ul class="list-group list-group-flush">
                @forelse($lowStockProducts as $p)
                    <li class="list-group-item d-flex justify-content-between"><span>{{ $p->name }}</span><span class="badge bg-danger">{{ $p->stock_qty }} left</span></li>
                @empty
                    <li class="list-group-item text-muted">No low stock items 🎉</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header fw-bold text-warning"><i class="bi bi-hourglass-split"></i> Expiring Soon (90 days)</div>
            <ul class="list-group list-group-flush">
                @forelse($expiringProducts as $p)
                    <li class="list-group-item d-flex justify-content-between"><span>{{ $p->name }}</span><span class="badge bg-warning text-dark">{{ $p->expiry_date->format("d M Y") }}</span></li>
                @empty
                    <li class="list-group-item text-muted">Nothing expiring soon</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-receipt"></i> Recent Sales</div>
            <ul class="list-group list-group-flush">
                @forelse($recentSales as $s)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $s->invoice_no }} <small class="text-muted">({{ $s->customer->name ?? "Walk-in" }})</small></span>
                        <b>৳{{ number_format($s->grand_total,2) }}</b>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No sales yet</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
const money = (v) => "৳" + Number(v).toLocaleString();

new Chart(document.getElementById("salesTrendChart"), {
    type: "line",
    data: {
        labels: {!! json_encode($salesTrend->pluck("ym")) !!},
        datasets: [
            { label: "Sales", data: {!! json_encode($salesTrend->pluck("total")) !!}, borderColor: "#4f46e5", backgroundColor: "rgba(79,70,229,.12)", fill: true, tension: .35 },
            { label: "Profit", data: {!! json_encode($salesTrend->pluck("profit")) !!}, borderColor: "#10b981", backgroundColor: "rgba(16,185,129,.12)", fill: true, tension: .35 },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("categoryStockChart"), {
    type: "doughnut",
    data: {
        labels: {!! json_encode($categoryStock->pluck("category.name")) !!},
        datasets: [{ data: {!! json_encode($categoryStock->pluck("value")) !!}, backgroundColor: ["#4f46e5","#10b981","#f59e0b","#ef4444","#06b6d4","#8b5cf6","#ec4899","#14b8a6"] }]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("salesPurchaseChart"), {
    type: "bar",
    data: {
        labels: {!! json_encode($last7Days->map(fn($d) => $d->format("D"))) !!},
        datasets: [
            { label: "Sales", data: {!! json_encode($dailySales) !!}, backgroundColor: "#4f46e5" },
            { label: "Purchase", data: {!! json_encode($dailyPurchase) !!}, backgroundColor: "#f59e0b" },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("topProductsChart"), {
    type: "pie",
    data: {
        labels: {!! json_encode($topProducts->pluck("product.name")) !!},
        datasets: [{ data: {!! json_encode($topProducts->pluck("qty")) !!}, backgroundColor: ["#4f46e5","#10b981","#f59e0b","#ef4444","#06b6d4"] }]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});
</script>
@endpush
