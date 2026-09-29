<?php $__env->startSection('title', 'Purchases'); ?>
<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Purchases</h4>
        <a href="<?php echo e(route('admin.purchases.create')); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Purchase</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('admin.purchases.show', $p)); ?>"><?php echo e($p->invoice_no); ?></a></td>
                            <td><?php echo e($p->supplier->name ?? '-'); ?></td>
                            <td><?php echo e($p->purchase_date->format('d M Y')); ?></td>
                            <td>৳<?php echo e(number_format($p->grand_total, 2)); ?></td>
                            <td>৳<?php echo e(number_format($p->paid_amount, 2)); ?></td>
                            <td>৳<?php echo e(number_format($p->due_amount, 2)); ?></td>
                            <td>
                                <?php if($p->status === 'cancelled'): ?>
                                    <span class="badge bg-danger">Returned</span>
                                <?php else: ?>
                                    <span
                                        class="badge bg-<?php echo e($p->payment_status === 'paid' ? 'success' : 'warning'); ?>"><?php echo e(ucfirst($p->payment_status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.purchases.show', $p)); ?>"
                                    class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <?php if($p->status !== 'cancelled'): ?>
                                    <form action="<?php echo e(route('admin.purchases.destroy', $p)); ?>" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Return this purchase to the supplier? Unsold stock will be reversed.')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger" title="Return to supplier"><i
                                                class="bi bi-arrow-return-left"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No purchases found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($purchases->links()); ?></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/purchases/index.blade.php ENDPATH**/ ?>