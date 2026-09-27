<?php $__env->startSection('title', 'Categories'); ?>
<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Categories</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add
            Category</button>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($row->name); ?></td>
                            <td><?php echo e($row->products()->count()); ?></td>
                            <td>
                                <img src="<?php echo e($row->image_url); ?>" alt="<?php echo e($row->name); ?>" class="img-fluid" width="80">
                            </td>
                            <td><span
                                    class="badge bg-<?php echo e($row->status ? 'success' : 'secondary'); ?>"><?php echo e($row->status ? 'Active' : 'Inactive'); ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo e($row->id); ?>"><i class="bi bi-pencil"></i></button>
                                <form action="<?php echo e(route('admin.categories.destroy', $row)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button
                                        class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        <div class="modal fade" id="editModal<?php echo e($row->id); ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?php echo e(route('admin.categories.update', $row)); ?>" method="POST"
                                        enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-header">
                                            <h6 class="modal-title">Edit Category</h6><button class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control mb-2"
                                                value="<?php echo e($row->name); ?>" required>

                                            <label class="form-label">Image</label>
                                            <input type="file" name="image" class="form-control mb-2" accept="image/*">
                                            <div class="form-check"><input type="checkbox" name="status" value="1"
                                                    class="form-check-input" <?php echo e($row->status ? 'checked' : ''); ?>><label
                                                    class="form-check-label">Active</label></div>
                                        </div>
                                        <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($categorys->links()); ?></div>
    </div>
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST" enctype="multipart/form-data"><?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h6 class="modal-title">Add Category</h6><button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Name</label><input type="text" name="name" class="form-control mb-2"
                            required>
                        <label class="form-label">Image</label><input type="file" name="image" class="form-control">
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>