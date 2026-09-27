@extends("layouts.admin")
@section("title", "Profit & Loss")
@section("content")
<h4 class="mb-3">Profit &amp; Loss Statement</h4>
@include("admin.reports._range_filter")

<div class="card mb-3">
    <div class="card-body">
        <table class="table table-borderless">
            <tr><td>Total Revenue (Sales)</td><td class="text-end fw-bold">৳{{ number_format($revenue,2) }}</td></tr>
            <tr><td>Cost of Goods Sold (COGS)</td><td class="text-end text-danger">- ৳{{ number_format($cogs,2) }}</td></tr>
            <tr class="border-top"><td class="fw-bold">Gross Profit</td><td class="text-end fw-bold">৳{{ number_format($grossProfit,2) }}</td></tr>
            <tr><td>Operating Expenses</td><td class="text-end text-danger">- ৳{{ number_format($expenseTotal,2) }}</td></tr>
            <tr class="border-top fs-5"><td class="fw-bold">Net Profit / Loss</td>
                <td class="text-end fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    ৳{{ number_format($netProfit,2) }} {{ $netProfit >= 0 ? "(Profit)" : "(Loss)" }}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="card"><div class="card-header fw-bold">Monthly Revenue vs Gross Profit</div>
<div class="card-body"><canvas id="plChart" height="100"></canvas></div></div>
@endsection
@push("scripts")
<script>
new Chart(document.getElementById("plChart"), {
    type: "bar",
    data: {
        labels: {!! json_encode($monthly->pluck("ym")) !!},
        datasets: [
            { label: "Revenue", data: {!! json_encode($monthly->pluck("revenue")) !!}, backgroundColor: "#4f46e5" },
            { label: "Gross Profit", data: {!! json_encode($monthly->pluck("gross_profit")) !!}, backgroundColor: "#10b981" },
        ]
    },
    options: { responsive: true, plugins:{legend:{position:"bottom"}} }
});
</script>
@endpush
