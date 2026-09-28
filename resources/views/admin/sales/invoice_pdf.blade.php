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
            font-size: 26px;
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

        .customer-box {
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

    @php($phone = $sale->branch->phone ?? ($siteSettings['phone'] ?? null))
    @php($email = $sale->branch->email ?? ($siteSettings['email'] ?? null))
    @php($custPhone = $sale->customer_phone ?? ($sale->customer->phone ?? null))

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
                    @if ($sale->branch?->name)
                        <b style="color:#111827; font-size:11px">{{ $sale->branch->name }}</b><br>
                    @endif
                    @if ($sale->branch?->address)
                        {{ $sale->branch->address }}<br>
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
                <div class="invoice-title">INVOICE</div>
                <div class="muted" style="margin-top:4px">
                    <b>No:</b> {{ $sale->invoice_no }}<br>
                    <b>Date:</b> {{ $sale->sale_date->format('d M Y') }}<br>
                    <b>Channel:</b> {{ strtoupper($sale->channel) }}
                </div>
                <div style="margin-top:6px">
                    @if ($sale->due_amount > 0)
                        <span class="badge badge-due">DUE</span>
                    @else
                        <span class="badge badge-paid">PAID</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- ===== Row 2: Customer details ===== --}}
    <div class="customer-box">
        <div class="label">Bill To</div>
        <div class="muted">
            <b style="color:#111827; font-size:11px">
                {{ $sale->customer->name ?? ($sale->customer_name ?? 'Walk-in Customer') }}
            </b><br>
            @if ($custPhone)
                Phone: {{ $custPhone }}<br>
            @endif
            @if ($sale->customer->email ?? null)
                Email: {{ $sale->customer->email }}<br>
            @endif
            @if ($sale->customer->address ?? null)
                {{ $sale->customer->address }}
            @endif
        </div>
    </div>

    {{-- ===== Items ===== --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th>Product</th>
                <th class="text-center" style="width:15%">Qty</th>
                <th class="text-end" style="width:13%">Price</th>
                <th class="text-end" style="width:13%">Discount</th>
                <th class="text-end" style="width:15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $i => $item)
                <tr class="{{ $i % 2 ? 'alt' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product->name ?? 'Deleted product' }}</td>
                    <td class="text-center">{{ $item->unit_qty ?? $item->quantity }}
                        {{ $item->productUnit->unit->name ?? 'pcs' }}</td>
                    <td class="text-end">{{ number_format($item->sale_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->discount, 2) }}</td>
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
                        <td class="text-end">{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td class="text-end">- {{ number_format($sale->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td class="text-end">{{ number_format($sale->tax, 2) }}</td>
                    </tr>
                    @if ($sale->shipping_cost > 0)
                        <tr>
                            <td>Shipping</td>
                            <td class="text-end">{{ number_format($sale->shipping_cost, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand">
                        <td>Grand Total</td>
                        <td class="text-end">{{ number_format($sale->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Paid</td>
                        <td class="text-end">{{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="due">
                        <td>Due</td>
                        <td class="text-end">{{ number_format($sale->due_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== Barcode ===== --}}
    {{-- <div class="codes">
        {!! \App\Services\BarcodeService::svg($sale->invoice_no) !!}
        <div class="muted">Scan for invoice reference — {{ $sale->invoice_no }}</div>
    </div> --}}
    {{-- ===== Barcode ===== --}}
    <div class="codes">
        @php($barcodeSvg = \App\Services\BarcodeService::svg($sale->invoice_no))
        <img src="data:image/svg+xml;base64,{{ base64_encode($barcodeSvg) }}" style="height:50px" alt="Barcode">
        <div class="muted">Scan for invoice reference — {{ $sale->invoice_no }}</div>
    </div>

    {{-- ===== Footer ===== --}}
    <div class="footer muted">
        Thank you for choosing {{ $siteSettings['site_name'] ?? config('app.name') }}.
        This is a computer-generated invoice.
    </div>

</body>

</html>
