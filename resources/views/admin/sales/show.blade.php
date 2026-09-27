@extends("layouts.admin")
@section("title", "Invoice " . $sale->invoice_no)
@section("content")
<div class="d-flex justify-content-between mb-3">
    <h4>Invoice {{ $sale->invoice_no }}</h4>
    <div>
        <a href="{{ route('admin.sales.pdf', $sale) }}" class="btn btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6"><b>Customer:</b> {{ $sale->customer->name ?? "Walk-in Customer" }}</div>
            <div class="col-6 text-end"><b>Date:</b> {{ $sale->sale_date->format("d M Y") }}</div>
        </div>
        <table class="table">
            <thead><tr><th>Product</th><th>Qty</th><th>Price/Unit</th><th>Discount</th><th>Subtotal</th></tr></thead>
            <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? "Deleted product" }}</td>
                    <td>{{ $item->unit_qty ?? $item->quantity }} {{ $item->productUnit->unit->name ?? $item->product->unit->short_name ?? "pcs" }}
                        @if($item->unit_qty && $item->productUnit?->conversion_factor > 1)<br><small class="text-muted">({{ $item->quantity }} pcs total)</small>@endif
                    </td>
                    <td>৳{{ number_format($item->sale_price,2) }}</td>
                    <td>৳{{ number_format($item->discount,2) }}</td>
                    <td>৳{{ number_format($item->subtotal,2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="row justify-content-end">
            <div class="col-md-4">
                <table class="table table-sm">
                    <tr><td>Subtotal</td><td class="text-end">৳{{ number_format($sale->total_amount,2) }}</td></tr>
                    <tr><td>Discount</td><td class="text-end">৳{{ number_format($sale->discount,2) }}</td></tr>
                    <tr><td>Tax</td><td class="text-end">৳{{ number_format($sale->tax,2) }}</td></tr>
                    <tr class="fw-bold"><td>Grand Total</td><td class="text-end">৳{{ number_format($sale->grand_total,2) }}</td></tr>
                    <tr><td>Paid</td><td class="text-end">৳{{ number_format($sale->paid_amount,2) }}</td></tr>
                    <tr><td>Due</td><td class="text-end">৳{{ number_format($sale->due_amount,2) }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
