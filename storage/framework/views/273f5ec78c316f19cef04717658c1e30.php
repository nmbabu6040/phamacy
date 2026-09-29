<?php $__env->startSection("title", "Sales Report"); ?>
<?php $__env->startSection("content"); ?>
<h4 class="mb-3">Sales Report — Daily / Weekly / Monthly / Yearly</h4>
<?php echo $__env->make("admin.reports._range_filter", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-receipt text-primary"></i><div><b><?php echo e($summary["count"]); ?></b><span>Total Invoices</span></div></div></div>
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-cash text-success"></i><div><b>৳<?php echo e(number_format($summary["total"],2)); ?></b><span>Total Sales</span></div></div></div>
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-graph-up text-info"></i><div><b>৳<?php echo e(number_format($summary["profit"],2)); ?></b><span>Total Profit</span></div></div></div>
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-exclamation-circle text-danger"></i><div><b>৳<?php echo e(number_format($summary["due"],2)); ?></b><span>Total Due</span></div></div></div>
</div>
<div class="card mb-3"><div class="card-header fw-bold">Sales Chart</div><div class="card-body"><canvas id="salesChart" height="90"></canvas></div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Total</th><th>Profit</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><a href="<?php echo e(route('admin.sales.show',$s)); ?>"><?php echo e($s->invoice_no); ?></a></td><td><?php echo e($s->customer->name ?? "Walk-in"); ?></td><td><?php echo e($s->sale_date->format("d M Y")); ?></td><td>৳<?php echo e(number_format($s->grand_total,2)); ?></td><td>৳<?php echo e(number_format($s->profit,2)); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No sales in this period.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-footer"><?php echo e($list->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush("scripts"); ?>
<script>
new Chart(document.getElementById("salesChart"), {
    type: "line",
    data: { labels: <?php echo json_encode($chart->pluck("d")); ?>, datasets: [{ label: "Sales", data: <?php echo json_encode($chart->pluck("total")); ?>, borderColor: "#4f46e5", backgroundColor: "rgba(79,70,229,.15)", fill: true, tension:.3 }] },
    options: { responsive: true }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/reports/sales.blade.php ENDPATH**/ ?>