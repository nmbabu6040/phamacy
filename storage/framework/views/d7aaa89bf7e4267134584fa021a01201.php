<?php $__env->startSection("title", "Your Cart"); ?>
<?php $__env->startSection("content"); ?>
<div class="page-banner"><div class="container"><h2 data-aos="fade-up">Your Cart</h2>
<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li><li class="breadcrumb-item active">Cart</li></ol></nav></div></div>

<section class="py-5">
    <div class="container">
        <?php if(session("success")): ?><div class="alert alert-success"><?php echo e(session("success")); ?></div><?php endif; ?>
        <?php if(session("error")): ?><div class="alert alert-danger"><?php echo e(session("error")); ?></div><?php endif; ?>

        <?php if(count($items)): ?>
        <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($row["product"]->name); ?></td>
                    <td>৳<?php echo e(number_format($row["product"]->sale_price,2)); ?></td>
                    <td style="width:140px">
                        <form action="<?php echo e(route('cart.update', $row["product"])); ?>" method="POST" class="d-flex">
                            <?php echo csrf_field(); ?> <?php echo method_field("PATCH"); ?>
                            <input type="number" name="quantity" value="<?php echo e($row["qty"]); ?>" min="1" max="<?php echo e($row["product"]->stock_qty); ?>" class="form-control form-control-sm me-1">
                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button>
                        </form>
                    </td>
                    <td>৳<?php echo e(number_format($row["line_total"],2)); ?></td>
                    <td>
                        <form action="<?php echo e(route('cart.remove', $row["product"])); ?>" method="POST" onsubmit="return confirm('Remove item?')">
                            <?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
        <div class="d-flex justify-content-end">
            <div class="text-end">
                <h5>Subtotal: ৳<?php echo e(number_format($subtotal,2)); ?></h5>
                <a href="<?php echo e(route('checkout.index')); ?>" class="btn btn-primary btn-lg mt-2"><i class="bi bi-bag-check"></i> Proceed to Checkout</a>
            </div>
        </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <h5 class="mt-3">Your cart is empty</h5>
                <a href="<?php echo e(route('shop.index')); ?>" class="btn btn-primary mt-2">Browse Medicines</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.frontend", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/shop/cart.blade.php ENDPATH**/ ?>