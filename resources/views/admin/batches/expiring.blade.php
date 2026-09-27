@extends("layouts.admin")
@section("title", "Expiry Alerts")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Expiry Alerts</h4>
    <form method="GET" class="d-flex gap-2">
        <select name="days" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="30" @selected($days==30)>Next 30 days</option>
            <option value="90" @selected($days==90)>Next 90 days</option>
            <option value="180" @selected($days==180)>Next 180 days</option>
        </select>
        <button name="notify" value="1" class="btn btn-sm btn-warning"><i class="bi bi-envelope"></i> Email Alert to Admins</button>
    </form>
</div>

@if(request("notify"))<div class="alert alert-success">Expiry alert email queued to Admin &amp; Manager users.</div>@endif

<div class="card mb-4">
    <div class="card-header fw-bold text-danger"><i class="bi bi-exclamation-octagon"></i> Already Expired ({{ $alreadyExpired->count() }})</div>
    <div class="table-responsive"><table class="table mb-0">
        <thead class="table-light"><tr><th>Product</th><th>Batch No</th><th>Branch</th><th>Qty</th><th>Expired On</th></tr></thead>
        <tbody>
        @forelse($alreadyExpired as $b)
            <tr class="table-danger"><td>{{ $b->product->name }}</td><td>{{ $b->batch_no }}</td><td>{{ $b->branch->name }}</td><td>{{ $b->quantity }}</td><td>{{ $b->expiry_date->format("d M Y") }}</td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-3">No expired stock. 🎉</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="card">
    <div class="card-header fw-bold text-warning"><i class="bi bi-hourglass-split"></i> Expiring Within {{ $days }} Days ({{ $expiringSoon->count() }})</div>
    <div class="table-responsive"><table class="table mb-0">
        <thead class="table-light"><tr><th>Product</th><th>Batch No</th><th>Branch</th><th>Qty</th><th>Expires On</th></tr></thead>
        <tbody>
        @forelse($expiringSoon as $b)
            <tr class="table-warning"><td>{{ $b->product->name }}</td><td>{{ $b->batch_no }}</td><td>{{ $b->branch->name }}</td><td>{{ $b->quantity }}</td><td>{{ $b->expiry_date->format("d M Y") }}</td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-3">Nothing expiring in this window.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
