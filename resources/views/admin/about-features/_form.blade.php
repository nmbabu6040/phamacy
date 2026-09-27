<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Feature Text *</label>
        <input type="text" name="text" class="form-control" placeholder="e.g. 100% Genuine Medicine"
            value="{{ old('text', $item->text ?? '') }}" required>
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
</div>
