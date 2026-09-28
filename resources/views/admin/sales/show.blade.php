@extends('layouts.admin')
@section('title', 'Invoice ' . $sale->invoice_no)

@push('styles')
    <style>
        .invoice-card .invoice-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 2px;
        }

        .invoice-card .label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4f46e5;
            font-weight: 700;
            margin-bottom: .25rem;
        }

        .invoice-card .party-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: .9rem 1.1rem;
        }

        .invoice-card .items-table thead th {
            background: #4f46e5;
            color: #fff;
            border: none;
            font-size: .85rem;
        }

        .invoice-card .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        .invoice-card .grand-row td {
            background: #4f46e5 !important;
            color: #fff;
            font-weight: 700;
        }

        .invoice-card .due-row td {
            color: #ef4444;
            font-weight: 700;
        }

        [data-bs-theme="dark"] .invoice-card .party-box,
        [data-bs-theme="dark"] .invoice-card .items-table tbody tr:nth-child(even) td {
            background: #0f172a;
        }

        @media print {
            body {
                overflow: visible !important;
            }

            .sidebar,
            .topbar,
            .no-print {
                display: none !important;
            }

            .admin-wrapper,
            .main-content,
            .page-content {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
            }

            .invoice-card {
                box-shadow: none !important;
            }

            .invoice-card .items-table thead th,
            .invoice-card .grand-row td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $siteSettings = $siteSettings ?? \App\Models\Setting::pluck('value', 'key');
        $phone = $sale->branch->phone ?? ($siteSettings['phone'] ?? null);
        $email = $sale->branch->email ?? ($siteSettings['email'] ?? null);
        $custPhone = $sale->customer_phone ?? ($sale->customer->phone ?? null);
    @endphp

    {{-- ===== Action bar (screen only) ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h4 class="mb-0">Invoice {{ $sale->invoice_no }}</h4>
        <div>
            <a href="{{ route('admin.sales.pdf', $sale) }}" class="btn btn-outline-primary"><i
                    class="bi bi-file-earmark-pdf"></i> Download PDF</a>
            <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i>
                Print</button>
        </div>
    </div>

    <div class="card print-area invoice-card">
        <div class="card-body p-4">

            {{-- ===== Row 1: Logo + company details (left) | Invoice details (right) ===== --}}
            <div class="row g-3 pb-3 mb-3 border-bottom border-2 border-primary">
                <div class="col-7">
                    @if (!empty($siteSettings['site_logo'] ?? null))
                        <img src="{{ Storage::url($siteSettings['site_logo']) }}" style="height:55px" alt="Logo">
                    @else
                        <div class="fs-3 fw-bold text-primary">{{ $siteSettings['site_name'] ?? config('app.name') }}
                        </div>
                    @endif

                    <div class="text-muted small mt-2" style="line-height:1.7">
                        @if ($sale->branch?->name)
                            <b class="text-body">{{ $sale->branch->name }}</b><br>
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
                </div>

                <div class="col-5 text-end">
                    <div class="invoice-title">INVOICE</div>
                    <div class="text-muted small mt-1" style="line-height:1.7">
                        <b>No:</b> {{ $sale->invoice_no }}<br>
                        <b>Date:</b> {{ $sale->sale_date->format('d M Y') }}<br>
                        <b>Channel:</b> {{ strtoupper($sale->channel) }}
                    </div>
                    <div class="mt-2">
                        @if ($sale->due_amount > 0)
                            <span class="badge bg-danger">DUE</span>
                        @else
                            <span class="badge bg-success">PAID</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== Row 2: Customer details ===== --}}
            <div class="party-box mb-4">
                <div class="label">Bill To</div>
                <div class="text-muted small" style="line-height:1.7">
                    <b
                        class="text-body">{{ $sale->customer->name ?? ($sale->customer_name ?? 'Walk-in Customer') }}</b><br>
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
            <div class="table-responsive">
                <table class="table align-middle items-table">
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price/Unit</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->product->name ?? 'Deleted product' }}</td>
                                <td class="text-center">
                                    {{ $item->unit_qty ?? $item->quantity }}
                                    {{ $item->productUnit->unit->name ?? ($item->product->unit->short_name ?? 'pcs') }}
                                    @if ($item->unit_qty && $item->productUnit?->conversion_factor > 1)
                                        <br><small class="text-muted">({{ $item->quantity }} pcs total)</small>
                                    @endif
                                </td>
                                <td class="text-end">৳{{ number_format($item->sale_price, 2) }}</td>
                                <td class="text-end">৳{{ number_format($item->discount, 2) }}</td>
                                <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ===== Totals ===== --}}
            <div class="row justify-content-end">
                <div class="col-md-5 col-lg-4">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">৳{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td class="text-end">- ৳{{ number_format($sale->discount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td class="text-end">৳{{ number_format($sale->tax, 2) }}</td>
                        </tr>
                        @if ($sale->shipping_cost > 0)
                            <tr>
                                <td>Shipping</td>
                                <td class="text-end">৳{{ number_format($sale->shipping_cost, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="grand-row">
                            <td>Grand Total</td>
                            <td class="text-end">৳{{ number_format($sale->grand_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Paid</td>
                            <td class="text-end">৳{{ number_format($sale->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="due-row">
                            <td>Due</td>
                            <td class="text-end">৳{{ number_format($sale->due_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ===== Barcode ===== --}}
            <div class="text-center mt-4">
                {!! \App\Services\BarcodeService::svg($sale->invoice_no) !!}
                <div class="text-muted small">Scan for invoice reference — {{ $sale->invoice_no }}</div>
            </div>

            <div class="text-center text-muted small border-top pt-2 mt-4">
                Thank you for choosing {{ $siteSettings['site_name'] ?? config('app.name') }}.
            </div>
        </div>
    </div>
@endsection
