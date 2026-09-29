<?php $__env->startSection("title", "Sales"); ?>
<?php $__env->startSection("content"); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Sales</h4>
    <a href="<?php echo e(route('admin.sales.pos')); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Sale</a>
</div>
<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-4"><input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search invoice no..."></div>
        <div class="col-md-3"><input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control"></div>
        <div class="col-md-3"><input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card">
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><a href="<?php echo e(route('admin.sales.show', $s)); ?>"><?php echo e($s->invoice_no); ?></a></td>
                <td><?php echo e($s->customer->name ?? "Walk-in"); ?></td>
                <td><?php echo e($s->sale_date->format("d M Y")); ?></td>
                <td>৳<?php echo e(number_format($s->grand_total,2)); ?></td>
                <td>৳<?php echo e(number_format($s->paid_amount,2)); ?></td>
                <td>৳<?php echo e(number_format($s->due_amount,2)); ?></td>
                <td><span class="badge bg-<?php echo e($s->status === 'completed' ? 'success' : 'secondary'); ?>"><?php echo e(ucfirst($s->status)); ?></span></td>
                <td>
                    <a href="<?php echo e(route('admin.sales.show', $s)); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                    <?php if($s->status === "completed"): ?>
                    <form action="<?php echo e(route('admin.sales.destroy', $s)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Cancel this sale and restore stock?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">No sales found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    <div class="card-footer"><?php echo e($sales->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/sales/index.blade.php ENDPATH**/ ?>