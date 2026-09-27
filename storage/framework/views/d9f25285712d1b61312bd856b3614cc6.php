<?php $__env->startSection("title", "Products"); ?>

<?php $__env->startSection("content"); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Products / Medicines</h4>
    <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2" method="GET">
            <div class="col-md-4"><input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search by name or code..."></div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(request('category_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="stock" class="form-select">
                    <option value="">All Stock</option>
                    <option value="low" <?php if(request('stock') === 'low'): echo 'selected'; endif; ?>>Low Stock Only</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Generic</th><th>Category</th><th>Price</th><th>Stock</th><th>Expiry</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><b><?php echo e($p->name); ?></b><br><small class="text-muted"><?php echo e($p->code); ?> · <?php echo e($p->strength); ?></small></td>
                    <td><?php echo e($p->generic->name ?? "-"); ?></td>
                    <td><?php echo e($p->category->name ?? "-"); ?></td>
                    <td>৳<?php echo e(number_format($p->sale_price,2)); ?></td>
                    <td>
                        <span class="badge <?php echo e($p->isLowStock() ? 'bg-danger' : 'bg-success'); ?>"><?php echo e($p->stock_qty); ?> <?php echo e($p->unit->short_name ?? ''); ?></span>
                        <button class="btn btn-sm btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#stockModal<?php echo e($p->id); ?>"><i class="bi bi-arrow-repeat"></i></button>
                    </td>
                    <td><?php if($p->expiry_date): ?><span class="<?php echo e($p->isExpired() ? 'text-danger' : ($p->isExpiringSoon() ? 'text-warning' : '')); ?>"><?php echo e($p->expiry_date->format("d M Y")); ?></span><?php else: ?> - <?php endif; ?></td>
                    <td><span class="badge <?php echo e($p->status ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($p->status ? "Active" : "Inactive"); ?></span></td>
                    <td>
                        <a href="<?php echo e(route('admin.products.edit', $p)); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="<?php echo e(route('admin.products.destroy', $p)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <!-- Stock adjust modal -->
                <div class="modal fade" id="stockModal<?php echo e($p->id); ?>" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content">
                        <form action="<?php echo e(route('admin.products.stock', $p)); ?>" method="POST"><?php echo csrf_field(); ?>
                        <div class="modal-header"><h6 class="modal-title">Adjust Stock — <?php echo e($p->name); ?></h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <div class="mb-2"><label class="form-label">Direction</label>
                                <select name="direction" class="form-select"><option value="in">Stock In (+)</option><option value="out">Stock Out (-)</option></select>
                            </div>
                            <div class="mb-2"><label class="form-label">Quantity</label><input type="number" name="quantity" min="1" class="form-control" required></div>
                            <div class="mb-2"><label class="form-label">Note</label><input type="text" name="note" class="form-control"></div>
                        </div>
                        <div class="modal-footer"><button class="btn btn-primary">Update Stock</button></div>
                        </form>
                    </div></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"><?php echo e($products->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/products/index.blade.php ENDPATH**/ ?>