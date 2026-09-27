@extends("layouts.admin")
@section("title", "Online Orders")
@section("content")
<h4 class="mb-3">Online Orders (Storefront Checkout)</h4>
<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-5"><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search invoice or phone..."></div>
        <div class="col-md-4">
            <select name="status" class="form-select"><option value="">All Statuses</option>
                @foreach(["pending","processing","shipped","delivered","cancelled"] as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Phone</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($orders as $o)
            <tr>
                <td>{{ $o->invoice_no }}</td><td>{{ $o->customer_name }}</td><td>{{ $o->customer_phone }}</td>
                <td>৳{{ number_format($o->grand_total,2) }}</td>
                <td><span class="badge bg-{{ $o->payment_status==='paid' ? 'success' : 'warning' }}">{{ ucfirst($o->payment_status) }}</span></td>
                <td><span class="badge bg-{{ match($o->order_status){'delivered'=>'success','cancelled'=>'danger','shipped'=>'info','processing'=>'primary',default=>'secondary'} }}">{{ ucfirst($o->order_status) }}</span></td>
                <td>{{ $o->sale_date->format("d M Y") }}</td>
                <td><a href="{{ route('admin.orders.show',$o) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No online orders yet.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $orders->links() }}</div>
</div>
@endsection
