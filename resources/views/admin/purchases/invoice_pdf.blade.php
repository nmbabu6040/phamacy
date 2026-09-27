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
            <div class="brand">{{ config("app.name") }} — Purchase Invoice</div>
            <div class="muted">{{ $purchase->branch->name ?? "" }}</div>
        </div>
        <div style="text-align:right">
            <div><b>Invoice:</b> {{ $purchase->invoice_no }}</div>
            <div><b>Date:</b> {{ $purchase->purchase_date->format("d M Y") }}</div>
        </div>
    </div>

    <p><b>Supplier:</b> {{ $purchase->supplier->name ?? "-" }} @if($purchase->supplier?->phone) &nbsp;|&nbsp; {{ $purchase->supplier->phone }} @endif</p>

    <table>
        <thead><tr><th>#</th><th>Product</th><th>Batch No</th><th>Qty</th><th>Cost Price</th><th>Expiry</th><th>Subtotal</th></tr></thead>
        <tbody>
        @foreach($purchase->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product->name ?? "Deleted product" }}</td>
                <td>{{ $item->batch_no ?? "-" }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->purchase_price, 2) }}</td>
                <td>{{ $item->expiry_date?->format("d M Y") ?? "-" }}</td>
                <td>{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table style="margin-top:10px">
        <tr class="totals"><td style="width:70%"></td><td>Subtotal</td><td class="text-end">{{ number_format($purchase->total_amount,2) }}</td></tr>
        <tr class="totals"><td></td><td>Discount</td><td class="text-end">{{ number_format($purchase->discount,2) }}</td></tr>
        <tr class="totals"><td></td><td>Tax</td><td class="text-end">{{ number_format($purchase->tax,2) }}</td></tr>
        <tr class="totals"><td></td><td>Shipping</td><td class="text-end">{{ number_format($purchase->shipping_cost,2) }}</td></tr>
        <tr class="totals" style="font-weight:bold"><td></td><td>Grand Total</td><td class="text-end">{{ number_format($purchase->grand_total,2) }}</td></tr>
        <tr class="totals"><td></td><td>Paid</td><td class="text-end">{{ number_format($purchase->paid_amount,2) }}</td></tr>
        <tr class="totals"><td></td><td>Due</td><td class="text-end">{{ number_format($purchase->due_amount,2) }}</td></tr>
    </table>

    <div class="codes">
        {!! \App\Services\BarcodeService::svg($purchase->invoice_no) !!}
    </div>
</body>
</html>
