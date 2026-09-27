@extends("layouts.admin")
@section("title", "Expense Report")
@section("content")
<h4 class="mb-3">Expense Report</h4>
@include("admin.reports._range_filter")
<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="mini-card"><i class="bi bi-wallet2 text-danger"></i><div><b>৳{{ number_format($total,2) }}</b><span>Total Expenses</span></div></div></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card"><div class="card-header fw-bold">By Category</div>
        <ul class="list-group list-group-flush">
            @forelse($byCategory as $b)
                <li class="list-group-item d-flex justify-content-between"><span>{{ $b->category->name ?? "-" }}</span><b>৳{{ number_format($b->total,2) }}</b></li>
            @empty
                <li class="list-group-item text-muted">No data</li>
            @endforelse
        </ul></div>
    </div>
    <div class="col-md-6"><div class="card"><div class="card-body"><canvas id="expChart"></canvas></div></div></div>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>
        @forelse($list as $e)
            <tr><td>{{ $e->title }}</td><td>{{ $e->category->name ?? "-" }}</td><td>৳{{ number_format($e->amount,2) }}</td><td>{{ $e->expense_date->format("d M Y") }}</td></tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No expenses in this period.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $list->links() }}</div>
</div>
@endsection
@push("scripts")
<script>
new Chart(document.getElementById("expChart"), {
    type: "pie",
    data: { labels: {!! json_encode($byCategory->pluck("category.name")) !!}, datasets: [{ data: {!! json_encode($byCategory->pluck("total")) !!}, backgroundColor: ["#4f46e5","#10b981","#f59e0b","#ef4444","#06b6d4","#8b5cf6","#ec4899"] }] },
    options: { responsive: true, plugins:{legend:{position:"bottom"}} }
});
</script>
@endpush
