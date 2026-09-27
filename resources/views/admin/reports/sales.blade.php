@extends("layouts.admin")
@section("title", "Sales Report")
@section("content")
<h4 class="mb-3">Sales Report — Daily / Weekly / Monthly / Yearly</h4>
@include("admin.reports._range_filter")
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-receipt text-primary"></i><div><b>{{ $summary["count"] }}</b><span>Total Invoices</span></div></div></div>
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-cash text-success"></i><div><b>৳{{ number_format($summary["total"],2) }}</b><span>Total Sales</span></div></div></div>
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-graph-up text-info"></i><div><b>৳{{ number_format($summary["profit"],2) }}</b><span>Total Profit</span></div></div></div>
    <div class="col-md-3"><div class="mini-card"><i class="bi bi-exclamation-circle text-danger"></i><div><b>৳{{ number_format($summary["due"],2) }}</b><span>Total Due</span></div></div></div>
</div>
<div class="card mb-3"><div class="card-header fw-bold">Sales Chart</div><div class="card-body"><canvas id="salesChart" height="90"></canvas></div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Total</th><th>Profit</th></tr></thead>
        <tbody>
        @forelse($list as $s)
            <tr><td><a href="{{ route('admin.sales.show',$s) }}">{{ $s->invoice_no }}</a></td><td>{{ $s->customer->name ?? "Walk-in" }}</td><td>{{ $s->sale_date->format("d M Y") }}</td><td>৳{{ number_format($s->grand_total,2) }}</td><td>৳{{ number_format($s->profit,2) }}</td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No sales in this period.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $list->links() }}</div>
</div>
@endsection
@push("scripts")
<script>
new Chart(document.getElementById("salesChart"), {
    type: "line",
    data: { labels: {!! json_encode($chart->pluck("d")) !!}, datasets: [{ label: "Sales", data: {!! json_encode($chart->pluck("total")) !!}, borderColor: "#4f46e5", backgroundColor: "rgba(79,70,229,.15)", fill: true, tension:.3 }] },
    options: { responsive: true }
});
</script>
@endpush
