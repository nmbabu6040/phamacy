@extends("layouts.admin")
@section("title", "Bulk Courier Upload")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Bulk Courier Booking</h4>
    <a href="{{ route("admin.courier.index") }}" class="btn btn-light">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Upload a CSV or Excel file with columns:
            <code>invoice_reference, recipient_name, recipient_phone, recipient_address, cod_amount, weight, item_description, note</code>.
            <a href="{{ route("admin.courier.template") }}" class="fw-bold">Download the template</a> to get the exact column headers.
        </div>

        <form action="{{ route("admin.courier.bulk.upload") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Book all rows with</label>
                    <select name="provider" class="form-select" required>
                        @foreach($providers as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">CSV / Excel File</label>
                    <input type="file" name="file" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
                </div>
            </div>
            <button class="btn btn-primary btn-lg mt-4"><i class="bi bi-upload"></i> Upload &amp; Book All</button>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header fw-bold">How this works</div>
    <div class="card-body small text-muted">
        <ol class="mb-0">
            <li>Every row is sent to the selected courier as a separate parcel booking (Steadfast uses its native bulk API in one request; other couriers are booked one call per row).</li>
            <li>After upload, you will be redirected to the bookings list showing how many succeeded/failed.</li>
            <li>Rows missing a name, phone, or address are skipped automatically and reported back to you.</li>
            <li>If <code>invoice_reference</code> is left blank for a row, a random reference is generated automatically.</li>
        </ol>
    </div>
</div>
@endsection
