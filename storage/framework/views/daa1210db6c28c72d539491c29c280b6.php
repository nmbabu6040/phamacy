<?php $__env->startSection("title", "Checkout"); ?>
<?php $__env->startSection("content"); ?>
<div class="page-banner"><div class="container"><h2 data-aos="fade-up">Checkout</h2>
<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('cart.index')); ?>">Cart</a></li><li class="breadcrumb-item active">Checkout</li></ol></nav></div></div>

<section class="py-5">
    <div class="container">
        <?php if($errors->any()): ?><div class="alert alert-danger"><?php echo e($errors->first()); ?></div><?php endif; ?>
        <?php if(session("error")): ?><div class="alert alert-danger"><?php echo e(session("error")); ?></div><?php endif; ?>
        <div class="row g-5">
            <div class="col-lg-7" data-aos="fade-right">
                <h5 class="mb-3">Delivery Details</h5>
                <form action="<?php echo e(route('checkout.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Full Name *</label><input name="name" class="form-control" value="<?php echo e(old('name')); ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Phone *</label><input name="phone" class="form-control" value="<?php echo e(old('phone')); ?>" required></div>
                        <div class="col-md-12"><label class="form-label">Email (optional)</label><input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>"></div>
                        <div class="col-md-12"><label class="form-label">Delivery Address *</label><textarea name="address" class="form-control" rows="3" required><?php echo e(old('address')); ?></textarea></div>
                        <div class="col-md-12">
                            <label class="form-label">Payment Method *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash on Delivery</option>
                                <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                                <option value="card">Card on Delivery</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-lg w-100 mt-4"><i class="bi bi-check2-circle"></i> Place Order</button>
                </form>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="card p-4">
                    <h5 class="mb-3">Order Summary</h5>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo e($row["product"]->name); ?> × <?php echo e($row["qty"]); ?></span>
                            <span>৳<?php echo e(number_format($row["line_total"],2)); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>৳<?php echo e(number_format($subtotal,2)); ?></span></div>
                    <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span>৳60.00</span></div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span>৳<?php echo e(number_format($subtotal + 60,2)); ?></span></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.frontend", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/shop/checkout.blade.php ENDPATH**/ ?>