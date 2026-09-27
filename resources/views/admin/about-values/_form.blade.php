<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Icon Class *</label>
        <input type="text" name="icon" class="form-control" placeholder="e.g. bi-shield-check"
            value="{{ old('icon', $item->icon ?? '') }}" required>
    </div>
    <div class="col-md-5">
        <label class="form-label">Title *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}"
            required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control"
            value="{{ old('sort_order', $item->sort_order ?? 0) }}">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check">
            <input type="checkbox" name="status" value="1" class="form-check-input" id="status"
                {{ old('status', $item->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="status">Active</label>
        </div>
    </div>
    <div class="col-md-12">
        <label class="form-label">Description *</label>
        <textarea name="description" rows="3" class="form-control" required>{{ old('description', $item->description ?? '') }}</textarea>
    </div>
    <div class="col-md-2">
        <div class="counter-icon"><i class="bi {{ old('icon', $item->icon ?? 'bi-shield-check') }}"></i></div>
        <small class="text-muted">Icon preview</small>
    </div>
</div>
