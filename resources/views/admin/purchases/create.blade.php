@extends("layouts.admin")
@section("title", "New Purchase")
@section("content")
<div class="card">
    <div class="card-header fw-bold">New Purchase (Stock In)</div>
    <div class="card-body">
        <form action="{{ route('admin.purchases.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label">Purchase Date *</label><input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                @if(($allBranches ?? collect())->count())
                <div class="col-md-4">
                    <label class="form-label">Receiving Branch</label>
                    <select name="branch_id" class="form-select">
                        @foreach($allBranches as $b)<option value="{{ $b->id }}" @selected(($currentBranch?->id)==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                @endif
            </div>

            <table class="table" id="itemsTable">
                <thead><tr><th>Product</th><th>Unit</th><th>Qty (in unit)</th><th>= Base Qty</th><th>Batch No</th><th>Purchase Price</th><th>Sale Price</th><th>Mfg. Date</th><th>Expiry</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
            <p class="text-muted small">Pick "Box" or "Strip" and the piece-equivalent is calculated automatically for stock. Leave Batch No blank to auto-generate one. Setting an Expiry Date here is how expiry tracking &amp; FEFO stock-out work — always fill it in for real stock.</p>
            <button type="button" id="addRow" class="btn btn-sm btn-outline-primary mb-3"><i class="bi bi-plus"></i> Add Item</button>

            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Discount</label><input type="number" step="0.01" name="discount" class="form-control" value="0"></div>
                <div class="col-md-3"><label class="form-label">Tax</label><input type="number" step="0.01" name="tax" class="form-control" value="0"></div>
                <div class="col-md-3"><label class="form-label">Shipping Cost</label><input type="number" step="0.01" name="shipping_cost" class="form-control" value="0"></div>
                <div class="col-md-3"><label class="form-label">Paid Amount</label><input type="number" step="0.01" name="paid_amount" class="form-control" value="0"></div>
            </div>
            <div class="mt-3"><label class="form-label">Note</label><textarea name="note" class="form-control"></textarea></div>
            <button class="btn btn-primary mt-4"><i class="bi bi-check2"></i> Save Purchase</button>
        </form>
    </div>
</div>

<script>
// Each product carries its own unit tiers (Piece/Strip/Box...) with conversion factors + default prices.
const products = @json($products->map(fn($p) => [
    "id" => $p->id, "name" => $p->name, "code" => $p->code,
    "purchase_price" => $p->purchase_price, "sale_price" => $p->sale_price,
    "units" => $p->units->map(fn($u) => [
        "id" => $u->id, "name" => $u->unit->name ?? "Unit", "factor" => $u->conversion_factor,
        "purchase_price" => $u->purchase_price, "sale_price" => $u->sale_price,
    ]),
]));
let rowIndex = 0;

function unitOptionsFor(product) {
    if (product.units && product.units.length) {
        return product.units.map(u => `<option value="${u.id}" data-factor="${u.factor}" data-pp="${u.purchase_price}" data-sp="${u.sale_price}">${u.name} (×${u.factor})</option>`).join("");
    }
    return `<option value="" data-factor="1" data-pp="${product.purchase_price}" data-sp="${product.sale_price}">Piece (×1)</option>`;
}

function addRow() {
    const options = products.map(p => `<option value="${p.id}" data-pp="${p.purchase_price}" data-sp="${p.sale_price}">${p.name} (${p.code})</option>`).join("");
    const row = `<tr data-row="${rowIndex}">
        <td><select name="items[${rowIndex}][product_id]" class="form-select product-select" required><option value="">Select product</option>${options}</select></td>
        <td><select name="items[${rowIndex}][product_unit_id]" class="form-select unit-select" style="width:130px"><option value="">Piece</option></select></td>
        <td><input type="number" name="items[${rowIndex}][unit_qty]" class="form-control unit-qty-input" min="1" value="1" style="width:80px"></td>
        <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control base-qty-input" min="1" value="1" required style="width:90px" readonly></td>
        <td><input type="text" name="items[${rowIndex}][batch_no]" class="form-control" placeholder="Auto" style="width:120px"></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][purchase_price]" class="form-control pp-input" required style="width:100px"></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][sale_price]" class="form-control sp-input" style="width:100px"></td>
        <td><input type="date" name="items[${rowIndex}][manufacturing_date]" class="form-control" style="width:150px"></td>
        <td><input type="date" name="items[${rowIndex}][expiry_date]" class="form-control" style="width:150px"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x"></i></button></td>
    </tr>`;
    $("#itemsTable tbody").append(row);
    rowIndex++;
}

function recalcBaseQty(row) {
    const factor = parseInt(row.find(".unit-select option:selected").data("factor")) || 1;
    const unitQty = parseInt(row.find(".unit-qty-input").val()) || 1;
    row.find(".base-qty-input").val(factor * unitQty);
}

$("#addRow").on("click", addRow);

$(document).on("change", ".product-select", function () {
    const productId = $(this).val();
    const product = products.find(p => p.id == productId);
    const row = $(this).closest("tr");
    if (!product) return;

    row.find(".unit-select").html(unitOptionsFor(product));
    const firstUnit = row.find(".unit-select option:first");
    row.find(".pp-input").val(firstUnit.data("pp") || product.purchase_price || 0);
    row.find(".sp-input").val(firstUnit.data("sp") || product.sale_price || 0);
    recalcBaseQty(row);
});

$(document).on("change", ".unit-select", function () {
    const opt = $(this).find(":selected");
    const row = $(this).closest("tr");
    row.find(".pp-input").val(opt.data("pp") || 0);
    row.find(".sp-input").val(opt.data("sp") || 0);
    recalcBaseQty(row);
});

$(document).on("input", ".unit-qty-input", function () { recalcBaseQty($(this).closest("tr")); });

$(document).on("click", ".remove-row", function () { $(this).closest("tr").remove(); });
addRow();
</script>
@endsection
