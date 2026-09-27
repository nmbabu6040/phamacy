<?php $__env->startSection('title', 'Homepage Sliders'); ?>
<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Homepage Sliders</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add
            Slide</button>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><img src="<?php echo e($row->image_url); ?>" width="80" class="rounded"
                                    onerror="this.src="https://via.placeholder.com/80?text=No+Image"></td>
                            <td><?php echo e($row->title); ?></td>
                            <td><?php echo e($row->sort_order); ?></td>
                            <td><span
                                    class="badge bg-<?php echo e($row->status ? 'success' : 'secondary'); ?>"><?php echo e($row->status ? 'Active' : 'Inactive'); ?></span>
                            </td>
                            
                            <td>

                                
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo e($row->id); ?>" title="Edit Slider">
                                    <i class="bi bi-pencil"></i>
                                </button>


                                
                                <form action="<?php echo e(route('admin.sliders.destroy', $row)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this slider?');">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?>

                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Slider">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            </td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No slides yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($sliders->links()); ?></div>
    </div>

    

    <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="editModal<?php echo e($row->id); ?>" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form action="<?php echo e(route('admin.sliders.update', $row)); ?>" method="POST" enctype="multipart/form-data">

                        <?php echo csrf_field(); ?>

                        <?php echo method_field('PUT'); ?>


                        
                        <div class="modal-header">

                            <h5 class="modal-title">
                                <i class="bi bi-pencil-square me-1"></i>
                                Edit Slider
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                        </div>


                        
                        <div class="modal-body">

                            <div class="row">


                                
                                <div class="col-md-12 mb-3">

                                    <label class="form-label">
                                        Title
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text" name="title" class="form-control"
                                        value="<?php echo e(old('title', $row->title)); ?>" required maxlength="255">

                                </div>


                                
                                <div class="col-md-12 mb-3">

                                    <label class="form-label">
                                        Subtitle
                                    </label>

                                    <textarea name="subtitle" class="form-control" rows="3" maxlength="1000"><?php echo e(old('subtitle', $row->subtitle)); ?></textarea>

                                </div>


                                
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Button Text
                                    </label>

                                    <input type="text" name="button_text" class="form-control"
                                        value="<?php echo e(old('button_text', $row->button_text)); ?>" maxlength="100">

                                </div>


                                
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Button Link
                                    </label>

                                    <input type="text" name="button_link" class="form-control"
                                        value="<?php echo e(old('button_link', $row->button_link)); ?>" maxlength="500"
                                        placeholder="https://example.com">

                                </div>


                                
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Current Image
                                    </label>

                                    <?php if($row->image): ?>
                                        <div>

                                            <img src="<?php echo e(Storage::url($row->image)); ?>" alt="<?php echo e($row->title); ?>"
                                                class="img-thumbnail"
                                                style="max-width: 220px; max-height: 120px; object-fit: cover;">

                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted">
                                            No image uploaded.
                                        </div>
                                    <?php endif; ?>

                                </div>


                                
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Change Image
                                    </label>

                                    <input type="file" name="image" class="form-control"
                                        accept=".jpg,.jpeg,.png,.webp">

                                    <div class="form-text">
                                        JPG, JPEG, PNG or WEBP. Maximum 2MB.
                                    </div>

                                </div>


                                
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Sort Order
                                    </label>

                                    <input type="number" name="sort_order" class="form-control" min="0"
                                        value="<?php echo e(old('sort_order', $row->sort_order ?? 0)); ?>">

                                </div>


                                
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select name="status" class="form-select">

                                        <option value="1" <?php echo e($row->status ? 'selected' : ''); ?>>
                                            Active
                                        </option>

                                        <option value="0" <?php echo e(!$row->status ? 'selected' : ''); ?>>
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>


                        
                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Update Slider
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('admin.sliders.store')); ?>" method="POST" enctype="multipart/form-data"><?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h6 class="modal-title">Add Slide</h6><button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Title</label><input name="title" class="form-control mb-2" required>
                        <label class="form-label">Subtitle</label><input name="subtitle" class="form-control mb-2">
                        <label class="form-label">Button Text</label><input name="button_text" class="form-control mb-2">
                        <label class="form-label">Button Link</label><input name="button_link" class="form-control mb-2">
                        <label class="form-label">Image</label><input type="file" name="image"
                            class="form-control mb-2">
                        <label class="form-label">Sort Order</label><input type="number" name="sort_order"
                            class="form-control" value="0">
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/sliders/index.blade.php ENDPATH**/ ?>