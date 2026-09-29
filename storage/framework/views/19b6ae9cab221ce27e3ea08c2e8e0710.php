<?php $__env->startSection("title", "Online Orders"); ?>
<?php $__env->startSection("content"); ?>
<h4 class="mb-3">Online Orders (Storefront Checkout)</h4>
<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-5"><input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search invoice or phone..."></div>
        <div class="col-md-4">
            <select name="status" class="form-select"><option value="">All Statuses</option>
                <?php $__currentLoopData = ["pending","processing","shipped","delivered","cancelled"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php if(request('status')===$s): echo 'selected'; endif; ?>><?php echo e(ucfirst($s)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Phone</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($o->invoice_no); ?></td><td><?php echo e($o->customer_name); ?></td><td><?php echo e($o->customer_phone); ?></td>
                <td>৳<?php echo e(number_format($o->grand_total,2)); ?></td>
                <td><span class="badge bg-<?php echo e($o->payment_status==='paid' ? 'success' : 'warning'); ?>"><?php echo e(ucfirst($o->payment_status)); ?></span></td>
                <td><span class="badge bg-<?php echo e(match($o->order_status){'delivered'=>'success','cancelled'=>'danger','shipped'=>'info','processing'=>'primary',default=>'secondary'}); ?>"><?php echo e(ucfirst($o->order_status)); ?></span></td>
                <td><?php echo e($o->sale_date->format("d M Y")); ?></td>
                <td><a href="<?php echo e(route('admin.orders.show',$o)); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">No online orders yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-footer"><?php echo e($orders->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>