
<div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
    <div class="d-flex align-items-center gap-3">
        <?php if(!empty($siteSettings['site_logo'])): ?>
            <img src="<?php echo e(asset('storage/' . $siteSettings['site_logo'])); ?>" alt="Logo"
                style="height:56px;max-width:120px;object-fit:contain">
        <?php endif; ?>
        <div>
            <h5 class="mb-0 fw-bold"><?php echo e($siteSettings['site_name'] ?? config('app.name')); ?></h5>
            <?php if(!empty($siteSettings['site_tagline'])): ?>
                <div class="small text-muted"><?php echo e($siteSettings['site_tagline']); ?></div>
            <?php endif; ?>
            <div class="small text-muted">
                <?php if($branch ?? null): ?>
                    <?php echo e($branch->name); ?><?php echo e($branch->address ? ' -- ' . $branch->address : ''); ?>

                <?php elseif(!empty($siteSettings['address'])): ?>
                    <?php echo e($siteSettings['address']); ?>

                <?php endif; ?>
            </div>
            <div class="small text-muted">
                <?php ($phone = $branch->phone ?? null ?: $siteSettings['phone'] ?? null); ?>
                <?php ($email = $branch->email ?? null ?: $siteSettings['email'] ?? null); ?>
                <?php if($phone): ?>
                    <i class="bi bi-telephone"></i> <?php echo e($phone); ?>

                <?php endif; ?>
                <?php if($phone && $email): ?>
                    &nbsp;|&nbsp;
                <?php endif; ?>
                <?php if($email): ?>
                    <i class="bi bi-envelope"></i> <?php echo e($email); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/partials/_invoice_letterhead.blade.php ENDPATH**/ ?>