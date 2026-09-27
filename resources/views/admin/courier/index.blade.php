@extends("layouts.admin")
@section("title", "Courier Booking")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Courier Booking</h4>
    <div>
        <a href="{{ route("admin.courier.bulk.form") }}" class="btn btn-outline-primary"><i class="bi bi-upload"></i> Bulk Upload</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adhocModal"><i class="bi bi-plus-lg"></i> Book Manually</button>
    </div>
</div>

@if($pendingOrders->count())
<div class="card mb-4">
    <div class="card-header fw-bold text-warning"><i class="bi bi-exclamation-circle"></i> Online Orders Awaiting Courier ({{ $pendingOrders->count() }})</div>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Phone</th><th>Address</th><th>COD Amount</th><th>Book With</th></tr></thead>
        <tbody>
        @foreach($pendingOrders as $order)
            <tr>
                <td>{{ $order->invoice_no }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->customer_phone }}</td>
                <td class="small">{{ \Illuminate\Support\Str::limit($order->shipping_address, 40) }}</td>
                <td>৳{{ number_format($order->payment_status === "paid" ? 0 : $order->due_amount, 2) }}</td>
                <td>
                    <form action="{{ route("admin.courier.book", $order) }}" method="POST" class="d-flex gap-1">
                        @csrf
                        <select name="provider" class="form-select form-select-sm" required>
                            @foreach($providers as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                        </select>
                        <button class="btn btn-sm btn-success"><i class="bi bi-truck"></i> Book</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-4"><input type="text" name="search" value="{{ request("search") }}" class="form-control" placeholder="Search invoice, phone, or consignment ID..."></div>
        <div class="col-md-3">
            <select name="provider" class="form-select"><option value="">All Couriers</option>
                @foreach($providers as $key => $label)<option value="{{ $key }}" @selected(request("provider")===$key)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select"><option value="">All Statuses</option>
                @foreach(["pending","processing","booked","in_transit","delivered","cancelled","failed"] as $s)
                    <option value="{{ $s }}" @selected(request("status")===$s)>{{ ucfirst(str_replace("_"," ",$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>

<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Reference</th><th>Courier</th><th>Recipient</th><th>Phone</th><th>COD</th><th>Consignment ID</th><th>Status</th><th>Booked</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($bookings as $b)
            <tr>
                <td>{{ $b->invoice_reference }} @if($b->sale)<br><small class="text-muted">Order: {{ $b->sale->invoice_no }}</small>@endif</td>
                <td><span class="badge bg-info text-dark">{{ $b->providerLabel() }}</span></td>
                <td>{{ $b->recipient_name }}</td>
                <td>{{ $b->recipient_phone }}</td>
                <td>৳{{ number_format($b->cod_amount,2) }}</td>
                <td>{{ $b->consignment_id ?? "-" }}</td>
                <td>
                    <span class="badge bg-{{ match($b->status){"delivered"=>"success","failed"=>"danger","cancelled"=>"secondary","in_transit"=>"primary","booked"=>"info",default=>"warning"} }}">{{ ucfirst(str_replace("_"," ",$b->status)) }}</span>
                    @if($b->status === "failed" && $b->failure_reason)<br><small class="text-danger">{{ Illuminate\Support\Str::limit($b->failure_reason, 50) }}</small>@endif
                </td>
                <td class="small text-muted">{{ $b->created_at->format("d M Y, h:i A") }}</td>
                <td>
                    @if($b->consignment_id)
                    <form action="{{ route("admin.courier.refresh", $b) }}" method="POST" class="d-inline">@csrf
                        <button class="btn btn-sm btn-outline-secondary" title="Refresh status"><i class="bi bi-arrow-repeat"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No courier bookings yet.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $bookings->links() }}</div>
</div>

<div class="modal fade" id="adhocModal"><div class="modal-dialog modal-lg"><div class="modal-content">
    <form action="{{ route("admin.courier.book-adhoc") }}" method="POST">@csrf
    <div class="modal-header"><h6 class="modal-title">Book a Courier Manually</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Courier</label>
                <select name="provider" class="form-select" required>
                    @foreach($providers as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Your Reference</label><input name="invoice_reference" class="form-control" placeholder="e.g. order number" required></div>
            <div class="col-md-4"><label class="form-label">COD Amount</label><input type="number" step="0.01" name="cod_amount" class="form-control" value="0" required></div>
            <div class="col-md-6"><label class="form-label">Recipient Name</label><input name="recipient_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Recipient Phone</label><input name="recipient_phone" class="form-control" required></div>
            <div class="col-md-12"><label class="form-label">Recipient Address</label><textarea name="recipient_address" class="form-control" required></textarea></div>
            <div class="col-md-4"><label class="form-label">Weight (kg)</label><input type="number" step="0.1" name="weight" class="form-control" value="0.5"></div>
            <div class="col-md-8"><label class="form-label">Item Description</label><input name="item_description" class="form-control" placeholder="e.g. Medicine parcel"></div>
            <div class="col-md-12"><label class="form-label">Note (optional)</label><input name="note" class="form-control"></div>
        </div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary"><i class="bi bi-truck"></i> Book Courier</button></div>
    </form>
</div></div></div>
@endsection
