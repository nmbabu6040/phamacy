<?php $__env->startSection("title", "Order Placed"); ?>
<?php $__env->startSection("content"); ?>
<section class="py-5">
    <div class="container text-center">
        <i class="bi bi-check-circle-fill text-success display-1" data-aos="zoom-in"></i>
        <h2 class="mt-3" data-aos="fade-up">Thank you, <?php echo e($sale->customer_name); ?>!</h2>
        <p class="text-muted" data-aos="fade-up">Your order has been placed successfully.</p>

        <div class="card mx-auto mt-4 p-4 text-start" style="max-width:500px" data-aos="fade-up">
            <p><b>Invoice No:</b> <?php echo e($sale->invoice_no); ?></p>
            <p><b>Status:</b> <span class="badge bg-secondary"><?php echo e(ucfirst($sale->order_status)); ?></span></p>
            <p><b>Total:</b> ৳<?php echo e(number_format($sale->grand_total,2)); ?></p>
            <p><b>Payment:</b> <?php echo e(ucfirst(str_replace("_"," ",$sale->payment_method))); ?></p>
            <hr>
            <p class="mb-0 small text-muted">A confirmation has been sent to your email/SMS. Save your invoice number and phone number to track your order.</p>
        </div>

        <div class="mt-4" data-aos="fade-up">
            <a href="<?php echo e(route('order.track')); ?>" class="btn btn-outline-primary me-2">Track This Order</a>
            <a href="<?php echo e(route('shop.index')); ?>" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.frontend", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/shop/order-success.blade.php ENDPATH**/ ?>