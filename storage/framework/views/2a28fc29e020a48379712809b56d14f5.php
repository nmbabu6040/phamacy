<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-3">
            <select name="range" class="form-select" onchange="this.form.submit()">
                <option value="daily" <?php if($range==="daily"): echo 'selected'; endif; ?>>Daily (Today)</option>
                <option value="weekly" <?php if($range==="weekly"): echo 'selected'; endif; ?>>Weekly</option>
                <option value="monthly" <?php if($range==="monthly"): echo 'selected'; endif; ?>>Monthly</option>
                <option value="yearly" <?php if($range==="yearly"): echo 'selected'; endif; ?>>Yearly</option>
                <option value="custom" <?php if($range==="custom"): echo 'selected'; endif; ?>>Custom Range</option>
            </select>
        </div>
        <div class="col-md-3"><input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control" placeholder="From"></div>
        <div class="col-md-3"><input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control" placeholder="To"></div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Apply</button></div>
    </form>
    <small class="text-muted">Showing: <?php echo e($from->format("d M Y")); ?> — <?php echo e($to->format("d M Y")); ?></small>
</div></div>
<?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/reports/_range_filter.blade.php ENDPATH**/ ?>