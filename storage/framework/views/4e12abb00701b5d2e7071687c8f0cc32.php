<?php $__env->startSection('title', 'Edit Product'); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header fw-bold">Edit Product — <?php echo e($product->name); ?></div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.products.update', $product)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <?php echo $__env->make('admin.products._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="mt-4"><button class="btn btn-primary">Update Product</button> <a
                        href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-light">Back</a></div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/products/edit.blade.php ENDPATH**/ ?>