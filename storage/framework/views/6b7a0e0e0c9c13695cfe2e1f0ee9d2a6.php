<?php $__env->startSection("title", "Shop | " . ($siteSettings["site_name"] ?? config("app.name"))); ?>

<?php $__env->startSection("content"); ?>
<div class="page-banner">
    <div class="container">
        <h2 data-aos="fade-up">Shop Medicines</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li><li class="breadcrumb-item active">Shop</li></ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="shop-sidebar" data-aos="fade-right">
                    <form method="GET">
                        <div class="mb-4">
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search medicine...">
                        </div>
                        <h6 class="mb-3">Categories</h6>
                        <ul class="filter-list mb-4">
                            <li><a href="<?php echo e(route('shop.index')); ?>" class="<?php echo e(!request('category') ? 'active' : ''); ?>">All Categories</a></li>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e(route('shop.index', ['category'=>$c->slug])); ?>" class="<?php echo e(request('category')===$c->slug ? 'active' : ''); ?>"><?php echo e($c->name); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <h6 class="mb-3">Generic Name</h6>
                        <ul class="filter-list">
                            <li><a href="<?php echo e(route('shop.index')); ?>" class="<?php echo e(!request('generic') ? 'active' : ''); ?>">All Generics</a></li>
                            <?php $__currentLoopData = $generics->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e(route('shop.index', ['generic'=>$g->slug])); ?>" class="<?php echo e(request('generic')===$g->slug ? 'active' : ''); ?>"><?php echo e($g->name); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </form>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
                    <span class="text-muted"><?php echo e($products->total()); ?> products found</span>
                    <form method="GET" class="d-flex align-items-center gap-2">
                        <?php $__currentLoopData = request()->except("sort"); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <label class="text-muted small mb-0">Sort:</label>
                        <select name="sort" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                            <option value="">Newest</option>
                            <option value="price_low" <?php if(request('sort')==='price_low'): echo 'selected'; endif; ?>>Price: Low to High</option>
                            <option value="price_high" <?php if(request('sort')==='price_high'): echo 'selected'; endif; ?>>Price: High to Low</option>
                        </select>
                    </form>
                </div>

                <div class="row g-4">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index * 40); ?>">
                            <div class="product-card">
                                <div class="product-thumb">
                                    <img src="<?php echo e($p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/300x220?text='.urlencode($p->name)); ?>" alt="<?php echo e($p->name); ?>">
                                    <?php if($p->isLowStock()): ?><span class="stock-badge">Low Stock</span><?php endif; ?>
                                    <a href="<?php echo e($p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/600'); ?>" class="quick-view venobox" data-gall="shop-products" title="<?php echo e($p->name); ?>"><i class="bi bi-eye"></i></a>
                                </div>
                                <div class="product-body">
                                    <span class="text-muted small"><?php echo e($p->generic->name ?? ""); ?> · <?php echo e($p->strength); ?></span>
                                    <h6 class="mb-1"><a href="<?php echo e(route('shop.show', $p)); ?>"><?php echo e($p->name); ?></a></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary">৳<?php echo e(number_format($p->sale_price,2)); ?></span>
                                        <a href="<?php echo e(route('shop.show', $p)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-center text-muted py-5">No products matched your search.</p>
                    <?php endif; ?>
                </div>

                <div class="mt-4"><?php echo e($products->links()); ?></div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.frontend", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/shop/index.blade.php ENDPATH**/ ?>