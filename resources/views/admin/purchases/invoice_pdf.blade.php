<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 30px 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #4f46e5;
        }

        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #4f46e5;
            letter-spacing: 2px;
        }

        .muted {
            color: #6b7280;
            font-size: 10px;
            line-height: 1.6;
        }

        .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4f46e5;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .divider {
            border-bottom: 2px solid #4f46e5;
            margin: 12px 0 14px;
        }

        .party-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
            border-radius: 3px;
        }

        .badge-paid {
            background: #10b981;
        }

        .badge-due {
            background: #ef4444;
        }

        .items {
            margin-top: 18px;
        }

        .items th {
            background: #4f46e5;
            color: #fff;
            padding: 8px;
            font-size: 11px;
            text-align: left;
        }

        .items td {
            padding: 7px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .items tr.alt td {
            background: #f9fafb;
        }

        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .totals td {
            padding: 4px 10px;
            font-size: 11px;
        }

        .totals .grand td {
            background: #4f46e5;
            color: #fff;
            font-weight: bold;
            font-size: 12px;
            padding: 7px 10px;
        }

        .totals .due td {
            font-weight: bold;
            color: #ef4444;
        }

        .codes {
            text-align: center;
            margin-top: 25px;
        }

        .footer {
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
    </style>
</head>

<body>

    @php($phone = $purchase->branch->phone ?? ($siteSettings['phone'] ?? null))
    @php($email = $purchase->branch->email ?? ($siteSettings['email'] ?? null))

    {{-- ===== Row 1: Logo + company details (left) | Invoice details (right) ===== --}}
    <table>
        <tr>
            <td style="width:55%; vertical-align:top">
                @if (!empty($siteSettings['site_logo'] ?? null))
                    <img src="{{ public_path('storage/' . $siteSettings['site_logo']) }}" style="height:50px"
                        alt="Logo">
                @else
                    <div class="brand">{{ $siteSettings['site_name'] ?? config('app.name') }}</div>
                @endif

                <div class="muted" style="margin-top:8px">
                    @if ($purchase->branch?->name)
                        <b style="color:#111827; font-size:11px">{{ $purchase->branch->name }}</b><br>
                    @endif
                    @if ($purchase->branch?->address)
                        {{ $purchase->branch->address }}<br>
                    @endif
                    @if ($phone)
                        Phone: {{ $phone }}<br>
                    @endif
                    @if ($email)
                        Email: {{ $email }}
                    @endif
                </div>
            </td>

            <td style="width:45%; vertical-align:top" class="text-end">
                <div class="invoice-title">PURCHASE INVOICE</div>
                <div class="muted" style="margin-top:4px">
                    <b>No:</b> {{ $purchase->invoice_no }}<br>
                    <b>Date:</b> {{ $purchase->purchase_date->format('d M Y') }}
                </div>
                <div style="margin-top:6px">
                    @if ($purchase->due_amount > 0)
                        <span class="badge badge-due">DUE</span>
                    @else
                        <span class="badge badge-paid">PAID</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- ===== Row 2: Supplier details ===== --}}
    <div class="party-box">
        <div class="label">Supplier</div>
        <div class="muted">
            <b style="color:#111827; font-size:11px">{{ $purchase->supplier->name ?? '-' }}</b><br>
            @if ($purchase->supplier?->phone)
                Phone: {{ $purchase->supplier->phone }}<br>
            @endif
            @if ($purchase->supplier?->email)
                Email: {{ $purchase->supplier->email }}<br>
            @endif
            @if ($purchase->supplier?->address)
                {{ $purchase->supplier->address }}
            @endif
        </div>
    </div>

    {{-- ===== Items ===== --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th>Product</th>
                <th style="width:14%">Batch No</th>
                <th class="text-center" style="width:8%">Qty</th>
                <th class="text-end" style="width:13%">Cost Price</th>
                <th class="text-center" style="width:13%">Expiry</th>
                <th class="text-end" style="width:14%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->items as $i => $item)
                <tr class="{{ $i % 2 ? 'alt' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product->name ?? 'Deleted product' }}</td>
                    <td>{{ $item->batch_no ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->purchase_price, 2) }}</td>
                    <td class="text-center">{{ $item->expiry_date?->format('d M Y') ?? '-' }}</td>
                    <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== Totals ===== --}}
    <table style="margin-top:12px">
        <tr>
            <td style="width:55%"></td>
            <td style="width:45%">
                <table class="totals">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-end">{{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td class="text-end">- {{ number_format($purchase->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td class="text-end">{{ number_format($purchase->tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Shipping</td>
                        <td class="text-end">{{ number_format($purchase->shipping_cost, 2) }}</td>
                    </tr>
                    <tr class="grand">
                        <td>Grand Total</td>
                        <td class="text-end">{{ number_format($purchase->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Paid</td>
                        <td class="text-end">{{ number_format($purchase->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="due">
                        <td>Due</td>
                        <td class="text-end">{{ number_format($purchase->due_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== Barcode ===== --}}
    <div class="codes">
        @php($barcodeSvg = \App\Services\BarcodeService::svg($purchase->invoice_no))
        <img src="data:image/svg+xml;base64,{{ base64_encode($barcodeSvg) }}" style="height:50px" alt="Barcode">
        <div class="muted">Purchase reference — {{ $purchase->invoice_no }}</div>
    </div>

    {{-- ===== Footer ===== --}}
    <div class="footer muted">
        This is a computer-generated purchase invoice from {{ $siteSettings['site_name'] ?? config('app.name') }}.
    </div>

</body>

</html>
