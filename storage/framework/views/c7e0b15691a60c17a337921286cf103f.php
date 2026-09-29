<?php $__env->startSection("title", "Backup"); ?>
<?php $__env->startSection("content"); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Database &amp; File Backup</h4>
    <form action="<?php echo e(route('admin.backups.run')); ?>" method="POST"><?php echo csrf_field(); ?>
        <button class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Run Backup Now</button>
    </form>
</div>
<div class="alert alert-info"><i class="bi bi-info-circle"></i> Backups are created via <code>spatie/laravel-backup</code> (<code>php artisan backup:run</code>) and stored under <code>storage/app/backup</code>. A daily automatic backup is scheduled at 2:00 AM (see <code>routes/console.php</code>) — make sure the server cron is configured to run <code>php artisan schedule:run</code> every minute.</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>File</th><th>Size</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><i class="bi bi-file-zip"></i> <?php echo e($f["name"]); ?></td>
                <td><?php echo e($f["size"]); ?></td>
                <td><?php echo e(\Carbon\Carbon::createFromTimestamp($f["date"])->format("d M Y, h:i A")); ?></td>
                <td>
                    <a href="<?php echo e(route('admin.backups.download', $f['name'])); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                    <form action="<?php echo e(route('admin.backups.destroy', $f['name'])); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this backup?')"><?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="text-center text-muted py-4">No backups yet. Click "Run Backup Now" to create one.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/backups/index.blade.php ENDPATH**/ ?>