
<?php $__env->startSection('title', 'Add FAQ'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header fw-bold">Add FAQ</div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.faqs.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo $__env->make('admin.faqs._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <button class="btn btn-primary mt-3">Save FAQ</button>
                <a href="<?php echo e(route('admin.faqs.index')); ?>" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/faqs/create.blade.php ENDPATH**/ ?>