<?php $__env->startSection("title", "Inventory Report"); ?>
<?php $__env->startSection("content"); ?>
<h4 class="mb-3">Inventory / Stock Report</h4>
<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="mini-card"><i class="bi bi-box-seam text-primary"></i><div><b>৳<?php echo e(number_format($totalStockValue,2)); ?></b><span>Total Stock Value (at cost)</span></div></div></div>
    <div class="col-md-6"><div class="mini-card"><i class="bi bi-exclamation-triangle text-danger"></i><div><b><?php echo e($lowStockCount); ?></b><span>Low Stock Items</span></div></div></div>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Product</th><th>Category</th><th>Generic</th><th>Stock</th><th>Purchase Price</th><th>Stock Value</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="<?php echo e($p->isLowStock() ? 'table-danger' : ''); ?>">
                <td><?php echo e($p->name); ?></td><td><?php echo e($p->category->name ?? "-"); ?></td><td><?php echo e($p->generic->name ?? "-"); ?></td>
                <td><?php echo e($p->stock_qty); ?></td><td>৳<?php echo e(number_format($p->purchase_price,2)); ?></td><td>৳<?php echo e(number_format($p->stock_qty*$p->purchase_price,2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-footer"><?php echo e($products->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/reports/inventory.blade.php ENDPATH**/ ?>