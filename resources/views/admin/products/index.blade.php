@extends("layouts.admin")
@section("title", "Products")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Products / Medicines</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2" method="GET">
            <div class="col-md-4"><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or code..."></div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="stock" class="form-select">
                    <option value="">All Stock</option>
                    <option value="low" @selected(request('stock') === 'low')>Low Stock Only</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Generic</th><th>Category</th><th>Price</th><th>Stock</th><th>Expiry</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td><b>{{ $p->name }}</b><br><small class="text-muted">{{ $p->code }} · {{ $p->strength }}</small></td>
                    <td>{{ $p->generic->name ?? "-" }}</td>
                    <td>{{ $p->category->name ?? "-" }}</td>
                    <td>৳{{ number_format($p->sale_price,2) }}</td>
                    <td>
                        <span class="badge {{ $p->isLowStock() ? 'bg-danger' : 'bg-success' }}">{{ $p->stock_qty }} {{ $p->unit->short_name ?? '' }}</span>
                        <button class="btn btn-sm btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#stockModal{{ $p->id }}"><i class="bi bi-arrow-repeat"></i></button>
                    </td>
                    <td>@if($p->expiry_date)<span class="{{ $p->isExpired() ? 'text-danger' : ($p->isExpiringSoon() ? 'text-warning' : '') }}">{{ $p->expiry_date->format("d M Y") }}</span>@else - @endif</td>
                    <td><span class="badge {{ $p->status ? 'bg-success' : 'bg-secondary' }}">{{ $p->status ? "Active" : "Inactive" }}</span></td>
                    <td>
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <!-- Stock adjust modal -->
                <div class="modal fade" id="stockModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content">
                        <form action="{{ route('admin.products.stock', $p) }}" method="POST">@csrf
                        <div class="modal-header"><h6 class="modal-title">Adjust Stock — {{ $p->name }}</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <div class="mb-2"><label class="form-label">Direction</label>
                                <select name="direction" class="form-select"><option value="in">Stock In (+)</option><option value="out">Stock Out (-)</option></select>
                            </div>
                            <div class="mb-2"><label class="form-label">Quantity</label><input type="number" name="quantity" min="1" class="form-control" required></div>
                            <div class="mb-2"><label class="form-label">Note</label><input type="text" name="note" class="form-control"></div>
                        </div>
                        <div class="modal-footer"><button class="btn btn-primary">Update Stock</button></div>
                        </form>
                    </div></div>
                </div>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $products->links() }}</div>
</div>
@endsection
