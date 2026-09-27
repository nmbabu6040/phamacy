<?php $__env->startSection("title", "Track Your Order"); ?>
<?php $__env->startSection("content"); ?>
<div class="page-banner"><div class="container"><h2 data-aos="fade-up">Track Your Order</h2></div></div>
<section class="py-5">
    <div class="container">
        <?php if(session("error")): ?><div class="alert alert-danger"><?php echo e(session("error")); ?></div><?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-lg-6" data-aos="fade-up">
                <form action="<?php echo e(route('order.track.find')); ?>" method="POST" class="card p-4 mb-4">
                    <?php echo csrf_field(); ?>
                    <label class="form-label">Invoice Number</label>
                    <input name="invoice_no" class="form-control mb-3" placeholder="ORD-XXXXXXXX" required>
                    <label class="form-label">Phone Number</label>
                    <input name="phone" class="form-control mb-3" required>
                    <button class="btn btn-primary">Track Order</button>
                </form>
            </div>
        </div>

        <?php if(isset($sale)): ?>
        <div class="row justify-content-center" data-aos="fade-up">
            <div class="col-lg-8">
                <div class="card p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Order <?php echo e($sale->invoice_no); ?></h5>
                        <span class="badge bg-primary fs-6"><?php echo e(ucfirst($sale->order_status)); ?></span>
                    </div>
                    <ul class="list-group list-group-flush mb-3">
                        <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php echo e($item->product->name ?? "Product"); ?> × <?php echo e($item->quantity); ?></span>
                                <span>৳<?php echo e(number_format($item->subtotal,2)); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>৳<?php echo e(number_format($sale->grand_total,2)); ?></span></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.frontend", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/shop/track.blade.php ENDPATH**/ ?>