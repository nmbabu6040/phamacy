<?php $__env->startSection("title", $product->name . " | " . ($siteSettings["site_name"] ?? config("app.name"))); ?>

<?php $__env->startSection("content"); ?>
<div class="page-banner">
    <div class="container">
        <h2 data-aos="fade-up"><?php echo e($product->name); ?></h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('shop.index')); ?>">Shop</a></li>
            <li class="breadcrumb-item active"><?php echo e($product->name); ?></li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <img src="<?php echo e($product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/500?text='.urlencode($product->name)); ?>" class="img-fluid rounded-4 shadow-sm w-100">
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="badge bg-primary-subtle text-primary mb-2"><?php echo e($product->category->name ?? ""); ?></span>
                <h2><?php echo e($product->name); ?></h2>
                <p class="text-muted">Generic: <b><?php echo e($product->generic->name ?? "-"); ?></b> &nbsp;|&nbsp; Strength: <b><?php echo e($product->strength ?? "-"); ?></b> &nbsp;|&nbsp; Form: <b><?php echo e($product->dosage_form); ?></b></p>
                <h3 class="text-primary mb-3">৳<?php echo e(number_format($product->sale_price,2)); ?></h3>
                <p><?php echo e($product->description ?: "No additional description provided for this product."); ?></p>
                <ul class="list-unstyled mb-4">
                    <li><i class="bi bi-check-circle text-success"></i> Brand: <?php echo e($product->brand->name ?? "-"); ?></li>
                    <li><i class="bi bi-check-circle text-success"></i> Availability:
                        <?php if($product->stock_qty > 0): ?><span class="text-success">In Stock (<?php echo e($product->stock_qty); ?> <?php echo e($product->unit->short_name ?? ''); ?>)</span><?php else: ?> <span class="text-danger">Out of Stock</span><?php endif; ?>
                    </li>
                    <?php if($product->expiry_date): ?><li><i class="bi bi-check-circle text-success"></i> Expiry: <?php echo e($product->expiry_date->format("d M Y")); ?></li><?php endif; ?>
                </ul>
                <?php if($product->stock_qty > 0): ?>
                <form action="<?php echo e(route('cart.add', $product)); ?>" method="POST" class="d-flex align-items-center gap-2">
                    <?php echo csrf_field(); ?>
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo e($product->stock_qty); ?>" class="form-control" style="width:90px">
                    <button class="btn btn-primary btn-lg"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                </form>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
                <?php endif; ?>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-upc-scan"></i> Product Code: <b><?php echo e($product->code); ?></b>
                </div>
            </div>
        </div>

        <?php if($related->count()): ?>
        <div class="mt-5">
            <h4 class="mb-4" data-aos="fade-up">Related Products</h4>
            <div class="row g-4">
                <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index*60); ?>">
                        <div class="product-card">
                            <div class="product-thumb"><img src="<?php echo e($p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/300x220'); ?>" alt="<?php echo e($p->name); ?>"></div>
                            <div class="product-body">
                                <h6><a href="<?php echo e(route('shop.show',$p)); ?>"><?php echo e($p->name); ?></a></h6>
                                <span class="fw-bold text-primary">৳<?php echo e(number_format($p->sale_price,2)); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.frontend", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/shop/show.blade.php ENDPATH**/ ?>