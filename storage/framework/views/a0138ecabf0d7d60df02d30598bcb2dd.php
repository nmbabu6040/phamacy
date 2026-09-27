
<?php $__env->startSection('title', 'Counters'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Homepage Counters</h4>
        <a href="<?php echo e(route('admin.counters.create')); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Counter</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mb-3" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search label..."
                    value="<?php echo e(request('search')); ?>">
            </form>

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Count</th>
                        <th>Label</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $counters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><i class="bi <?php echo e($c->icon); ?> fs-4"></i></td>
                            <td><?php echo e($c->count); ?></td>
                            <td><?php echo e($c->label); ?></td>
                            <td><?php echo e($c->sort_order); ?></td>
                            <td>
                                <?php if($c->status): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo e(route('admin.counters.edit', $c)); ?>" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('admin.counters.destroy', $c)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this counter?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No counters yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php echo e($counters->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/counters/index.blade.php ENDPATH**/ ?>