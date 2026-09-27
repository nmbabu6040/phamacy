<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Question *</label>
        <input type="text" name="question" class="form-control" value="{{ old('question', $faq->question ?? '') }}"
            required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control"
            value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check">
            <input type="checkbox" name="status" value="1" class="form-check-input" id="status"
                {{ old('status', $faq->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="status">Active</label>
        </div>
    </div>
    <div class="col-md-12">
        <label class="form-label">Answer *</label>
        <textarea name="answer" rows="4" class="form-control" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
    </div>
</div>
