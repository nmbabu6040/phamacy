@extends("layouts.admin")
@section("title", "Batches")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Product Batches (Stock Ledger)</h4>
    <a href="{{ route('admin.batches.expiring') }}" class="btn btn-outline-warning"><i class="bi bi-hourglass-split"></i> Expiry Alerts</a>
</div>
<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-4"><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search batch no or product..."></div>
        <div class="col-md-3">
            <select name="branch_id" class="form-select"><option value="">All Branches</option>
                @foreach($branches as $b)<option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select"><option value="">All</option>
                <option value="active" @selected(request('status')==='active')>Has Stock</option>
                <option value="empty" @selected(request('status')==='empty')>Depleted</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Batch No</th><th>Product</th><th>Branch</th><th>Remaining / Initial</th><th>Cost Price</th><th>Expiry</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($batches as $b)
            <tr class="{{ $b->isExpired() ? 'table-danger' : ($b->isExpiringSoon() ? 'table-warning' : '') }}">
                <td>{{ $b->batch_no }}</td>
                <td>{{ $b->product->name ?? "-" }}</td>
                <td>{{ $b->branch->name ?? "-" }}</td>
                <td>{{ $b->quantity }} / {{ $b->initial_quantity }}</td>
                <td>৳{{ number_format($b->purchase_price,2) }}</td>
                <td>{{ $b->expiry_date?->format("d M Y") ?? "-" }}</td>
                <td>
                    @if($b->quantity <= 0)<span class="badge bg-secondary">Depleted</span>
                    @elseif($b->isExpired())<span class="badge bg-danger">Expired</span>
                    @elseif($b->isExpiringSoon())<span class="badge bg-warning text-dark">Expiring Soon</span>
                    @else<span class="badge bg-success">Active</span>@endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No batches found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $batches->links() }}</div>
</div>
@endsection
