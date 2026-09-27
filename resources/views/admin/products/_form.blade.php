<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Product / Brand Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Product Code / SKU *</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $product->code ?? '') }}"
            required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Strength</label>
        <input type="text" name="strength" class="form-control" placeholder="e.g. 500mg"
            value="{{ old('strength', $product->strength ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Generic Name (Medicine)</label>
        <select name="generic_id" class="form-select">
            <option value="">-- Select --</option>
            @foreach ($generics as $g)
                <option value="{{ $g->id }}" @selected(old('generic_id', $product->generic_id ?? '') == $g->id)>{{ $g->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select">
            <option value="">-- Select --</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Brand / Manufacturer</label>
        <select name="brand_id" class="form-select">
            <option value="">-- Select --</option>
            @foreach ($brands as $b)
                <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $b->id)>{{ $b->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Unit</label>
        <select name="unit_id" class="form-select">
            <option value="">-- Select --</option>
            @foreach ($units as $u)
                <option value="{{ $u->id }}" @selected(old('unit_id', $product->unit_id ?? '') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Dosage Form *</label>
        <select name="dosage_form" class="form-select" required>
            @foreach (['Tablet', 'Capsule', 'Syrup', 'Injection', 'Cream', 'Drops', 'Inhaler', 'Other'] as $form)
                <option value="{{ $form }}" @selected(old('dosage_form', $product->dosage_form ?? '') == $form)>{{ $form }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Purchase Price *</label>
        <input type="number" step="0.01" name="purchase_price" class="form-control"
            value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Sale Price *</label>
        <input type="number" step="0.01" name="sale_price" class="form-control"
            value="{{ old('sale_price', $product->sale_price ?? 0) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tax %</label>
        <input type="number" step="0.01" name="tax_percent" class="form-control"
            value="{{ old('tax_percent', $product->tax_percent ?? 0) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Opening Stock Qty *</label>
        <input type="number" name="stock_qty" class="form-control"
            value="{{ old('stock_qty', $product->stock_qty ?? 0) }}" {{ isset($product) ? 'readonly' : '' }} required>
        @if (isset($product))
            <small class="text-muted">Use the Adjust Stock action on the list page to change stock.</small>
        @endif
    </div>
    <div class="col-md-3">
        <label class="form-label">Low Stock Alert Qty *</label>
        <input type="number" name="alert_qty" class="form-control"
            value="{{ old('alert_qty', $product->alert_qty ?? 10) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" class="form-control"
            value="{{ old('expiry_date', isset($product->expiry_date) ? $product->expiry_date->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Image</label>
        <input type="file" name="image" id="productImage" class="form-control" accept="image/*"
            onchange="previewProductImage(this)">
        <div class="mt-2">
            <img id="imagePreview"
                src="{{ isset($product) && $product->image ? asset('storage/' . $product->image) : '' }}"
                class="img-thumbnail {{ isset($product) && $product->image ? '' : 'd-none' }}"
                style="max-height: 150px;">
        </div>
    </div>

    <div class="col-md-8">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="col-md-4">
        <div class="form-check mt-4"><input type="checkbox" name="status" value="1" class="form-check-input"
                id="status" {{ old('status', $product->status ?? true) ? 'checked' : '' }}><label
                class="form-check-label" for="status">Active</label></div>
        <div class="form-check"><input type="checkbox" name="is_featured" value="1" class="form-check-input"
                id="feat" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}><label
                class="form-check-label" for="feat">Featured on homepage</label></div>
    </div>
</div>

<hr class="my-4">
<h6 class="mb-1">Selling Units &amp; Pricing <span class="text-muted small fw-normal">(optional — add Piece / Strip
        / Box / Bottle, each with its own rate)</span></h6>
<p class="text-muted small">Mark exactly one row as <b>Base</b> — that is the smallest unit (usually "Piece") stock is
    tracked in, so it must have a Conversion Factor of 1. A "Strip" of 10 tablets has factor 10; a "Box" of 10 strips
    has factor 100.</p>
<table class="table table-sm align-middle" id="unitsTable">
    <thead>
        <tr>
            <th style="width:40px">Base</th>
            <th>Unit</th>
            <th>1 unit = how many base units?</th>
            <th>Purchase Price</th>
            <th>Sale Price</th>
            <th>Barcode (optional)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($product->units ?? [] as $u)
            <tr>
                <td class="text-center"><input type="radio" name="units[{{ $loop->index }}][is_base_unit]"
                        value="1" class="form-check-input" {{ $u->is_base_unit ? 'checked' : '' }}></td>
                <td><select name="units[{{ $loop->index }}][unit_id]" class="form-select form-select-sm">
                        @foreach ($units as $un)
                            <option value="{{ $un->id }}" @selected($u->unit_id == $un->id)>{{ $un->name }}
                            </option>
                        @endforeach
                    </select></td>
                <td><input type="number" name="units[{{ $loop->index }}][conversion_factor]"
                        class="form-control form-control-sm" value="{{ $u->conversion_factor }}" min="1"
                        style="width:80px"></td>
                <td><input type="number" step="0.01" name="units[{{ $loop->index }}][purchase_price]"
                        class="form-control form-control-sm" value="{{ $u->purchase_price }}"></td>
                <td><input type="number" step="0.01" name="units[{{ $loop->index }}][sale_price]"
                        class="form-control form-control-sm" value="{{ $u->sale_price }}"></td>
                <td><input type="text" name="units[{{ $loop->index }}][barcode]"
                        class="form-control form-control-sm" value="{{ $u->barcode }}"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-unit-row"><i
                            class="bi bi-x"></i></button></td>
            </tr>
        @endforeach
    </tbody>
</table>
<button type="button" id="addUnitRow" class="btn btn-sm btn-outline-primary mb-3"><i class="bi bi-plus"></i> Add
    Unit</button>

<script>
    (function() {
        const availableUnits = @json($units->map(fn($u) => ['id' => $u->id, 'name' => $u->name]));
        let unitRowIndex = {{ ($product->units ?? collect())->count() }};

        window.addUnitRow = function(isBase = false) {
            const options = availableUnits.map(u => `<option value="${u.id}">${u.name}</option>`).join("");
            const row = `<tr>
            <td class="text-center"><input type="radio" name="units[${unitRowIndex}][is_base_unit]" value="1" class="form-check-input" ${isBase ? "checked" : ""}></td>
            <td><select name="units[${unitRowIndex}][unit_id]" class="form-select form-select-sm">${options}</select></td>
            <td><input type="number" name="units[${unitRowIndex}][conversion_factor]" class="form-control form-control-sm" value="1" min="1" style="width:80px"></td>
            <td><input type="number" step="0.01" name="units[${unitRowIndex}][purchase_price]" class="form-control form-control-sm" value="0"></td>
            <td><input type="number" step="0.01" name="units[${unitRowIndex}][sale_price]" class="form-control form-control-sm" value="0"></td>
            <td><input type="text" name="units[${unitRowIndex}][barcode]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-unit-row"><i class="bi bi-x"></i></button></td>
        </tr>`;
            document.querySelector("#unitsTable tbody").insertAdjacentHTML("beforeend", row);
            unitRowIndex++;
        };

        document.getElementById("addUnitRow").addEventListener("click", () => window.addUnitRow(false));
        document.getElementById("unitsTable").addEventListener("click", (e) => {
            if (e.target.closest(".remove-unit-row")) e.target.closest("tr").remove();
        });

        // Brand-new product with no unit rows yet? seed it with a starter "Piece" base row.
        if (unitRowIndex === 0) window.addUnitRow(true);
    })();
</script>
<script>
    function previewProductImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
