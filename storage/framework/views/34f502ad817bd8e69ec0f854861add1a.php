<?php $__env->startSection("title", "Purchase Report"); ?>
<?php $__env->startSection("content"); ?>
<h4 class="mb-3">Purchase Report</h4>
<?php echo $__env->make("admin.reports._range_filter", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="mini-card"><i class="bi bi-truck text-primary"></i><div><b><?php echo e($summary["count"]); ?></b><span>Total Purchases</span></div></div></div>
    <div class="col-md-4"><div class="mini-card"><i class="bi bi-cash text-success"></i><div><b>৳<?php echo e(number_format($summary["total"],2)); ?></b><span>Total Amount</span></div></div></div>
    <div class="col-md-4"><div class="mini-card"><i class="bi bi-exclamation-circle text-danger"></i><div><b>৳<?php echo e(number_format($summary["due"],2)); ?></b><span>Total Due</span></div></div></div>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Supplier</th><th>Date</th><th>Total</th><th>Due</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><a href="<?php echo e(route('admin.purchases.show',$p)); ?>"><?php echo e($p->invoice_no); ?></a></td><td><?php echo e($p->supplier->name ?? "-"); ?></td><td><?php echo e($p->purchase_date->format("d M Y")); ?></td><td>৳<?php echo e(number_format($p->grand_total,2)); ?></td><td>৳<?php echo e(number_format($p->due_amount,2)); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No purchases in this period.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-footer"><?php echo e($list->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/reports/purchases.blade.php ENDPATH**/ ?>