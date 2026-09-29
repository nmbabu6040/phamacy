<?php $__env->startSection("title", "Expense Report"); ?>
<?php $__env->startSection("content"); ?>
<h4 class="mb-3">Expense Report</h4>
<?php echo $__env->make("admin.reports._range_filter", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="mini-card"><i class="bi bi-wallet2 text-danger"></i><div><b>৳<?php echo e(number_format($total,2)); ?></b><span>Total Expenses</span></div></div></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card"><div class="card-header fw-bold">By Category</div>
        <ul class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $byCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="list-group-item d-flex justify-content-between"><span><?php echo e($b->category->name ?? "-"); ?></span><b>৳<?php echo e(number_format($b->total,2)); ?></b></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="list-group-item text-muted">No data</li>
            <?php endif; ?>
        </ul></div>
    </div>
    <div class="col-md-6"><div class="card"><div class="card-body"><canvas id="expChart"></canvas></div></div></div>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><?php echo e($e->title); ?></td><td><?php echo e($e->category->name ?? "-"); ?></td><td>৳<?php echo e(number_format($e->amount,2)); ?></td><td><?php echo e($e->expense_date->format("d M Y")); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="text-center text-muted py-4">No expenses in this period.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-footer"><?php echo e($list->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush("scripts"); ?>
<script>
new Chart(document.getElementById("expChart"), {
    type: "pie",
    data: { labels: <?php echo json_encode($byCategory->pluck("category.name")); ?>, datasets: [{ data: <?php echo json_encode($byCategory->pluck("total")); ?>, backgroundColor: ["#4f46e5","#10b981","#f59e0b","#ef4444","#06b6d4","#8b5cf6","#ec4899"] }] },
    options: { responsive: true, plugins:{legend:{position:"bottom"}} }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/reports/expenses.blade.php ENDPATH**/ ?>