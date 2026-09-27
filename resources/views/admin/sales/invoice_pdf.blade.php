<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    .header { display: flex; justify-content: space-between; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 15px; }
    .brand { font-size: 18px; font-weight: bold; color: #4f46e5; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-size: 11px; }
    th { background: #f3f4f6; }
    .totals td { border: none; padding: 3px 8px; }
    .text-end { text-align: right; }
    .muted { color: #6b7280; font-size: 10px; }
    .codes { text-align: center; margin-top: 15px; }
</style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">{{ config("app.name") }}</div>
            <div class="muted">{{ $sale->branch->name ?? "" }}</div>
            <div class="muted">{{ $sale->branch->address ?? "" }}</div>
        </div>
        <div style="text-align:right">
            <div><b>Invoice:</b> {{ $sale->invoice_no }}</div>
            <div><b>Date:</b> {{ $sale->sale_date->format("d M Y") }}</div>
            <div><b>Channel:</b> {{ strtoupper($sale->channel) }}</div>
        </div>
    </div>

    <p><b>Customer:</b> {{ $sale->customer->name ?? $sale->customer_name ?? "Walk-in Customer" }}
       @if($sale->customer_phone) &nbsp;|&nbsp; {{ $sale->customer_phone }} @endif</p>

    <table>
        <thead><tr><th>#</th><th>Product</th><th>Qty</th><th>Price</th><th>Discount</th><th>Subtotal</th></tr></thead>
        <tbody>
        @foreach($sale->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product->name ?? "Deleted product" }}</td>
                <td>{{ $item->unit_qty ?? $item->quantity }} {{ $item->productUnit->unit->name ?? "pcs" }}</td>
                <td>{{ number_format($item->sale_price, 2) }}</td>
                <td>{{ number_format($item->discount, 2) }}</td>
                <td>{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table style="margin-top:10px">
        <tr class="totals"><td style="width:70%"></td><td>Subtotal</td><td class="text-end">{{ number_format($sale->total_amount,2) }}</td></tr>
        <tr class="totals"><td></td><td>Discount</td><td class="text-end">{{ number_format($sale->discount,2) }}</td></tr>
        <tr class="totals"><td></td><td>Tax</td><td class="text-end">{{ number_format($sale->tax,2) }}</td></tr>
        @if($sale->shipping_cost > 0)<tr class="totals"><td></td><td>Shipping</td><td class="text-end">{{ number_format($sale->shipping_cost,2) }}</td></tr>@endif
        <tr class="totals" style="font-weight:bold"><td></td><td>Grand Total</td><td class="text-end">{{ number_format($sale->grand_total,2) }}</td></tr>
        <tr class="totals"><td></td><td>Paid</td><td class="text-end">{{ number_format($sale->paid_amount,2) }}</td></tr>
        <tr class="totals"><td></td><td>Due</td><td class="text-end">{{ number_format($sale->due_amount,2) }}</td></tr>
    </table>

    <div class="codes">
        {!! \App\Services\BarcodeService::svg($sale->invoice_no) !!}
        <div class="muted">Scan for invoice reference — {{ $sale->invoice_no }}</div>
    </div>

    <p class="muted" style="margin-top:20px">Thank you for choosing {{ config("app.name") }}. This is a computer-generated invoice.</p>
</body>
</html>
