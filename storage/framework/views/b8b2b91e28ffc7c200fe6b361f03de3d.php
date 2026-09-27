
<?php $__env->startSection('title', 'Add Feature'); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header fw-bold">Add Feature</div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.about-features.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo $__env->make('admin.about-features._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <button class="btn btn-primary mt-3">Save Feature</button>
                <a href="<?php echo e(route('admin.about-features.index')); ?>" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/about-features/create.blade.php ENDPATH**/ ?>