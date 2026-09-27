<?php $__env->startSection("title", "Dashboard"); ?>

<?php $__env->startSection("content"); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Welcome back, <?php echo e(auth()->user()->name); ?> 👋</h4>
    <span class="text-muted"><?php echo e(now()->format("l, d M Y")); ?></span>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-primary-soft">
            <div class="stat-icon text-primary"><i class="bi bi-cash-coin"></i></div>
            <div><div class="stat-value">৳<?php echo e(number_format($todaySales, 2)); ?></div><div class="stat-label">Today's Sales</div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-success-soft">
            <div class="stat-icon text-success"><i class="bi bi-graph-up-arrow"></i></div>
            <div><div class="stat-value">৳<?php echo e(number_format($monthProfit, 2)); ?></div><div class="stat-label">This Month Profit</div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-warning-soft">
            <div class="stat-icon text-warning"><i class="bi bi-exclamation-triangle"></i></div>
            <div><div class="stat-value"><?php echo e($lowStockCount); ?></div><div class="stat-label">Low Stock Items</div></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-danger-soft">
            <div class="stat-icon text-danger"><i class="bi bi-wallet2"></i></div>
            <div><div class="stat-value">৳<?php echo e(number_format($totalDue, 2)); ?></div><div class="stat-label">Total Due (Sales)</div></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-calendar3 text-primary"></i><div><b>৳<?php echo e(number_format($monthSales,2)); ?></b><span>This Month Sales</span></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-calendar-check text-info"></i><div><b>৳<?php echo e(number_format($yearSales,2)); ?></b><span>This Year Sales</span></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-truck text-secondary"></i><div><b>৳<?php echo e(number_format($monthPurchase,2)); ?></b><span>This Month Purchase</span></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="mini-card"><i class="bi bi-box-seam text-dark"></i><div><b><?php echo e($totalProducts); ?></b><span>Total Products</span></div></div></div>
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
                <?php $__empty_1 = true; $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item d-flex justify-content-between"><span><?php echo e($p->name); ?></span><span class="badge bg-danger"><?php echo e($p->stock_qty); ?> left</span></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-muted">No low stock items 🎉</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header fw-bold text-warning"><i class="bi bi-hourglass-split"></i> Expiring Soon (90 days)</div>
            <ul class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $expiringProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item d-flex justify-content-between"><span><?php echo e($p->name); ?></span><span class="badge bg-warning text-dark"><?php echo e($p->expiry_date->format("d M Y")); ?></span></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-muted">Nothing expiring soon</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-receipt"></i> Recent Sales</div>
            <ul class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?php echo e($s->invoice_no); ?> <small class="text-muted">(<?php echo e($s->customer->name ?? "Walk-in"); ?>)</small></span>
                        <b>৳<?php echo e(number_format($s->grand_total,2)); ?></b>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-muted">No sales yet</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush("scripts"); ?>
<script>
const money = (v) => "৳" + Number(v).toLocaleString();

new Chart(document.getElementById("salesTrendChart"), {
    type: "line",
    data: {
        labels: <?php echo json_encode($salesTrend->pluck("ym")); ?>,
        datasets: [
            { label: "Sales", data: <?php echo json_encode($salesTrend->pluck("total")); ?>, borderColor: "#4f46e5", backgroundColor: "rgba(79,70,229,.12)", fill: true, tension: .35 },
            { label: "Profit", data: <?php echo json_encode($salesTrend->pluck("profit")); ?>, borderColor: "#10b981", backgroundColor: "rgba(16,185,129,.12)", fill: true, tension: .35 },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("categoryStockChart"), {
    type: "doughnut",
    data: {
        labels: <?php echo json_encode($categoryStock->pluck("category.name")); ?>,
        datasets: [{ data: <?php echo json_encode($categoryStock->pluck("value")); ?>, backgroundColor: ["#4f46e5","#10b981","#f59e0b","#ef4444","#06b6d4","#8b5cf6","#ec4899","#14b8a6"] }]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("salesPurchaseChart"), {
    type: "bar",
    data: {
        labels: <?php echo json_encode($last7Days->map(fn($d) => $d->format("D"))); ?>,
        datasets: [
            { label: "Sales", data: <?php echo json_encode($dailySales); ?>, backgroundColor: "#4f46e5" },
            { label: "Purchase", data: <?php echo json_encode($dailyPurchase); ?>, backgroundColor: "#f59e0b" },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("topProductsChart"), {
    type: "pie",
    data: {
        labels: <?php echo json_encode($topProducts->pluck("product.name")); ?>,
        datasets: [{ data: <?php echo json_encode($topProducts->pluck("qty")); ?>, backgroundColor: ["#4f46e5","#10b981","#f59e0b","#ef4444","#06b6d4"] }]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>