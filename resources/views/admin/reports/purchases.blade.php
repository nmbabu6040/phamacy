@extends("layouts.admin")
@section("title", "Purchase Report")
@section("content")
<h4 class="mb-3">Purchase Report</h4>
@include("admin.reports._range_filter")
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="mini-card"><i class="bi bi-truck text-primary"></i><div><b>{{ $summary["count"] }}</b><span>Total Purchases</span></div></div></div>
    <div class="col-md-4"><div class="mini-card"><i class="bi bi-cash text-success"></i><div><b>৳{{ number_format($summary["total"],2) }}</b><span>Total Amount</span></div></div></div>
    <div class="col-md-4"><div class="mini-card"><i class="bi bi-exclamation-circle text-danger"></i><div><b>৳{{ number_format($summary["due"],2) }}</b><span>Total Due</span></div></div></div>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Supplier</th><th>Date</th><th>Total</th><th>Due</th></tr></thead>
        <tbody>
        @forelse($list as $p)
            <tr><td><a href="{{ route('admin.purchases.show',$p) }}">{{ $p->invoice_no }}</a></td><td>{{ $p->supplier->name ?? "-" }}</td><td>{{ $p->purchase_date->format("d M Y") }}</td><td>৳{{ number_format($p->grand_total,2) }}</td><td>৳{{ number_format($p->due_amount,2) }}</td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No purchases in this period.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $list->links() }}</div>
</div>
@endsection
