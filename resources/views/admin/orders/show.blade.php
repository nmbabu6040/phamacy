@extends("layouts.admin")
@section("title", "Order " . $sale->invoice_no)
@section("content")
<div class="d-flex justify-content-between mb-3">
    <h4>Order {{ $sale->invoice_no }}</h4>
    <a href="{{ route('admin.sales.pdf', $sale) }}" class="btn btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
</div>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card"><div class="card-body">
            <table class="table">
                <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                <tbody>
                @foreach($sale->items as $item)
                    <tr><td>{{ $item->product->name ?? "Deleted" }}</td><td>{{ $item->quantity }}</td><td>৳{{ number_format($item->sale_price,2) }}</td><td>৳{{ number_format($item->subtotal,2) }}</td></tr>
                @endforeach
                </tbody>
            </table>
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-sm">
                        <tr><td>Subtotal</td><td class="text-end">৳{{ number_format($sale->total_amount,2) }}</td></tr>
                        <tr><td>Shipping</td><td class="text-end">৳{{ number_format($sale->shipping_cost,2) }}</td></tr>
                        <tr class="fw-bold"><td>Grand Total</td><td class="text-end">৳{{ number_format($sale->grand_total,2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-header fw-bold">Customer</div><div class="card-body">
            <p class="mb-1"><b>{{ $sale->customer_name }}</b></p>
            <p class="mb-1"><i class="bi bi-telephone"></i> {{ $sale->customer_phone }}</p>
            @if($sale->customer_email)<p class="mb-1"><i class="bi bi-envelope"></i> {{ $sale->customer_email }}</p>@endif
            <p class="mb-0"><i class="bi bi-geo-alt"></i> {{ $sale->shipping_address }}</p>
        </div></div>
        <div class="card mb-3"><div class="card-header fw-bold">Order Status</div><div class="card-body">
            <form action="{{ route('admin.orders.status', $sale) }}" method="POST">
                @csrf
                <select name="order_status" class="form-select mb-2">
                    @foreach(["pending","processing","shipped","delivered","cancelled"] as $s)
                        <option value="{{ $s }}" @selected($sale->order_status===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary w-100">Update Status</button>
            </form>
        </div></div>

        <div class="card"><div class="card-header fw-bold"><i class="bi bi-truck"></i> Courier</div><div class="card-body">
            @if($sale->courierBooking)
                <p class="mb-1"><b>{{ $sale->courierBooking->providerLabel() }}</b></p>
                <p class="mb-1 small">Consignment: {{ $sale->courierBooking->consignment_id ?? "pending" }}</p>
                <span class="badge bg-info text-dark">{{ ucfirst(str_replace('_',' ',$sale->courierBooking->status)) }}</span>
                <form action="{{ route('admin.courier.refresh', $sale->courierBooking) }}" method="POST" class="mt-2">@csrf
                    <button class="btn btn-sm btn-outline-secondary w-100"><i class="bi bi-arrow-repeat"></i> Refresh Status</button>
                </form>
            @else
                <form action="{{ route('admin.courier.book', $sale) }}" method="POST">
                    @csrf
                    <select name="provider" class="form-select mb-2" required>
                        @foreach(\App\Services\Couriers\CourierManager::available() as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-success w-100"><i class="bi bi-truck"></i> Book Courier</button>
                </form>
            @endif
        </div></div>
    </div>
</div>
@endsection
