<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Icon Class *</label>
        <input type="text" name="icon" class="form-control" placeholder="e.g. bi-capsule"
            value="<?php echo e(old('icon', $counter->icon ?? '')); ?>" required>
        <small class="text-muted">Bootstrap Icons class name (bi-...)</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Count *</label>
        <input type="number" name="count" class="form-control" value="<?php echo e(old('count', $counter->count ?? 0)); ?>"
            required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Label *</label>
        <input type="text" name="label" class="form-control" placeholder="e.g. Medicines"
            value="<?php echo e(old('label', $counter->label ?? '')); ?>" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control"
            value="<?php echo e(old('sort_order', $counter->sort_order ?? 0)); ?>">
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <div class="form-check">
            <input type="checkbox" name="status" value="1" class="form-check-input" id="status"
                <?php echo e(old('status', $counter->status ?? true) ? 'checked' : ''); ?>>
            <label class="form-check-label" for="status">Active</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="counter-icon"><i class="bi <?php echo e(old('icon', $counter->icon ?? 'bi-capsule')); ?>"></i></div>
        <small class="text-muted">Live preview of icon</small>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/counters/_form.blade.php ENDPATH**/ ?>