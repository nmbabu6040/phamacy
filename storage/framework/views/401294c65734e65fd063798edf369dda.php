
<?php $__env->startSection('title', 'FAQs'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Frequently Asked Questions</h4>
        <a href="<?php echo e(route('admin.faqs.create')); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add FAQ</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mb-3" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search question..."
                    value="<?php echo e(request('search')); ?>">
            </form>

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($f->question); ?></td>
                            <td><?php echo e($f->sort_order); ?></td>
                            <td>
                                <?php if($f->status): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo e(route('admin.faqs.edit', $f)); ?>" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('admin.faqs.destroy', $f)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this FAQ?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No FAQs yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php echo e($faqs->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/faqs/index.blade.php ENDPATH**/ ?>