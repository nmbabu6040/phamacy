@extends("layouts.admin")
@section("title", "Inventory Report")
@section("content")
<h4 class="mb-3">Inventory / Stock Report</h4>
<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="mini-card"><i class="bi bi-box-seam text-primary"></i><div><b>৳{{ number_format($totalStockValue,2) }}</b><span>Total Stock Value (at cost)</span></div></div></div>
    <div class="col-md-6"><div class="mini-card"><i class="bi bi-exclamation-triangle text-danger"></i><div><b>{{ $lowStockCount }}</b><span>Low Stock Items</span></div></div></div>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Product</th><th>Category</th><th>Generic</th><th>Stock</th><th>Purchase Price</th><th>Stock Value</th></tr></thead>
        <tbody>
        @forelse($products as $p)
            <tr class="{{ $p->isLowStock() ? 'table-danger' : '' }}">
                <td>{{ $p->name }}</td><td>{{ $p->category->name ?? "-" }}</td><td>{{ $p->generic->name ?? "-" }}</td>
                <td>{{ $p->stock_qty }}</td><td>৳{{ number_format($p->purchase_price,2) }}</td><td>৳{{ number_format($p->stock_qty*$p->purchase_price,2) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No products found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $products->links() }}</div>
</div>
@endsection
