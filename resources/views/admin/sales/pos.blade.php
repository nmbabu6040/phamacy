@extends("layouts.admin")
@section("title", "POS - New Sale")

@section("content")
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-search"></i> Search Product (name / code / barcode)</div>
            <div class="card-body">
                <input type="text" id="posSearch" class="form-control form-control-lg mb-3" placeholder="Type medicine name or scan barcode...">
                <div id="posResults" class="row g-2"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span><i class="bi bi-cart3"></i> Cart</span>
                <span id="cartCount" class="badge bg-primary">0</span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Item</th><th>Unit</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr></thead>
                    <tbody id="cartBody"><tr id="emptyCartRow"><td colspan="6" class="text-center text-muted py-3">Cart is empty</td></tr></tbody>
                </table>
            </div>
            <div class="card-body border-top">
                <div class="mb-2">
                    <label class="form-label">Customer</label>
                    <select id="customerId" class="form-select">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>@endforeach
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6"><label class="form-label">Discount</label><input type="number" id="discount" class="form-control" value="0"></div>
                    <div class="col-6"><label class="form-label">Tax</label><input type="number" id="tax" class="form-control" value="0"></div>
                </div>
                <div class="d-flex justify-content-between fs-5 fw-bold"><span>Grand Total</span><span id="grandTotal">৳0.00</span></div>
                <div class="row g-2 mt-2">
                    <div class="col-6"><label class="form-label">Paid Amount</label><input type="number" id="paidAmount" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Payment Method</label>
                        <select id="paymentMethod" class="form-select">
                            <option value="cash">Cash</option><option value="card">Card</option>
                            <option value="mobile_banking">Mobile Banking</option><option value="due">Due</option>
                        </select>
                    </div>
                </div>
                <button id="checkoutBtn" class="btn btn-success btn-lg w-100 mt-3"><i class="bi bi-check2-circle"></i> Complete Sale</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
// Cart is keyed by "productId:productUnitId" so the same medicine can sit in the cart
// as both e.g. "2 Strip" and "5 Piece" in separate lines.
let cart = {};
let lastProducts = [];

function unitOptionsHtml(product, selectedUnitId) {
    return product.unit_options.map(u =>
        `<option value="${u.id ?? ''}" data-factor="${u.factor}" data-price="${u.sale_price}" ${String(u.id) === String(selectedUnitId) ? "selected" : ""}>${u.name} (×${u.factor})</option>`
    ).join("");
}

function renderCart() {
    const body = $("#cartBody").empty();
    const items = Object.values(cart);
    $("#cartCount").text(items.reduce((s, i) => s + i.unitQty, 0));

    if (!items.length) { body.html('<tr id="emptyCartRow"><td colspan="6" class="text-center text-muted py-3">Cart is empty</td></tr>'); }

    let total = 0;
    items.forEach(item => {
        const lineTotal = item.unitQty * item.price;
        total += lineTotal;
        const product = lastProducts.find(p => p.id == item.productId);
        body.append(`<tr data-key="${item.key}">
            <td>${item.name}</td>
            <td><select class="form-select form-select-sm cart-unit-select" style="width:120px">${product ? unitOptionsHtml(product, item.productUnitId) : `<option>${item.unitName}</option>`}</select></td>
            <td><input type="number" min="1" max="${Math.floor(item.baseStock / item.factor)}" value="${item.unitQty}" class="form-control form-control-sm qty-input" style="width:70px"></td>
            <td>৳${item.price.toFixed(2)}</td>
            <td>৳${lineTotal.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-outline-danger remove-item"><i class="bi bi-x"></i></button></td>
        </tr>`);
    });

    const discount = parseFloat($("#discount").val()) || 0;
    const tax = parseFloat($("#tax").val()) || 0;
    const grand = Math.max(total - discount + tax, 0);
    $("#grandTotal").text("৳" + grand.toFixed(2));
    $("#paidAmount").val(grand.toFixed(2));
}

// Live product search (debounced)
let searchTimer;
$("#posSearch").on("input", function () {
    clearTimeout(searchTimer);
    const q = $(this).val();
    searchTimer = setTimeout(() => {
        $.get("{{ route('admin.sales.pos.search') }}", { q }, function (products) {
            lastProducts = products;
            const box = $("#posResults").empty();
            if (!products.length) { box.html('<p class="text-muted px-2">No products found.</p>'); return; }
            products.forEach(p => {
                const defaultUnit = p.unit_options[0];
                box.append(`<div class="col-md-6">
                    <div class="pos-product-card" data-id="${p.id}" data-name="${p.name}" data-stock="${p.branch_stock ?? p.stock_qty}">
                        <div class="fw-bold">${p.name}</div>
                        <div class="text-muted small">${p.code} · ${p.dosage_form}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1 gap-2">
                            <select class="form-select form-select-sm product-unit-select" style="width:130px">${unitOptionsHtml(p, defaultUnit?.id)}</select>
                            <span class="badge bg-light text-dark">Stock: ${p.branch_stock ?? p.stock_qty}</span>
                        </div>
                    </div>
                </div>`);
            });
        });
    }, 300);
}).trigger("input");

// Add to cart (click the card itself, not the unit dropdown)
$(document).on("click", ".pos-product-card", function (e) {
    if ($(e.target).is("select, option")) return;

    const productId = $(this).data("id");
    const name = $(this).data("name");
    const baseStock = parseInt($(this).data("stock"));
    const unitSelect = $(this).find(".product-unit-select");
    const opt = unitSelect.find(":selected");
    const productUnitId = unitSelect.val() || null;
    const factor = parseInt(opt.data("factor")) || 1;
    const price = parseFloat(opt.data("price")) || 0;
    const unitName = opt.text();

    const key = `${productId}:${productUnitId}`;
    if (cart[key]) {
        if ((cart[key].unitQty + 1) * factor <= baseStock) cart[key].unitQty++;
    } else {
        cart[key] = { key, productId, productUnitId, unitName, factor, price, unitQty: 1, baseStock, name };
    }
    renderCart();
});

$(document).on("change", ".qty-input", function () {
    const key = $(this).closest("tr").data("key");
    let qty = parseInt($(this).val()) || 1;
    const maxQty = Math.floor(cart[key].baseStock / cart[key].factor);
    if (qty > maxQty) qty = maxQty;
    cart[key].unitQty = qty;
    renderCart();
});

// Switching the unit on an existing cart line re-prices it and resets qty to 1
$(document).on("change", ".cart-unit-select", function () {
    const row = $(this).closest("tr");
    const oldKey = row.data("key");
    const item = cart[oldKey];
    const opt = $(this).find(":selected");
    delete cart[oldKey];

    const newUnitId = $(this).val() || null;
    const newKey = `${item.productId}:${newUnitId}`;
    cart[newKey] = {
        ...item, key: newKey, productUnitId: newUnitId,
        factor: parseInt(opt.data("factor")) || 1,
        price: parseFloat(opt.data("price")) || 0,
        unitName: opt.text(), unitQty: 1,
    };
    renderCart();
});

$(document).on("click", ".remove-item", function () {
    delete cart[$(this).closest("tr").data("key")];
    renderCart();
});

$("#discount, #tax").on("input", renderCart);

$("#checkoutBtn").on("click", function () {
    const items = Object.values(cart).map(i => ({
        product_id: i.productId,
        product_unit_id: i.productUnitId,
        unit_qty: i.unitQty,
        sale_price: i.price,
        discount: 0,
    }));
    if (!items.length) { alert("Cart is empty!"); return; }

    $.ajax({
        url: "{{ route('admin.sales.store') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            customer_id: $("#customerId").val(),
            items,
            discount: $("#discount").val(),
            tax: $("#tax").val(),
            paid_amount: $("#paidAmount").val(),
            payment_method: $("#paymentMethod").val(),
        },
        success: function (res) {
            alert(res.message);
            window.location.href = res.invoice_url;
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.message || "Something went wrong.");
        }
    });
});

renderCart();
</script>
@endpush
