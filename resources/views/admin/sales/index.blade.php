@extends("layouts.admin")
@section("title", "Sales")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Sales</h4>
    <a href="{{ route('admin.sales.pos') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Sale</a>
</div>
<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-4"><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search invoice no..."></div>
        <div class="col-md-3"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
        <div class="col-md-3"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card">
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($sales as $s)
            <tr>
                <td><a href="{{ route('admin.sales.show', $s) }}">{{ $s->invoice_no }}</a></td>
                <td>{{ $s->customer->name ?? "Walk-in" }}</td>
                <td>{{ $s->sale_date->format("d M Y") }}</td>
                <td>৳{{ number_format($s->grand_total,2) }}</td>
                <td>৳{{ number_format($s->paid_amount,2) }}</td>
                <td>৳{{ number_format($s->due_amount,2) }}</td>
                <td><span class="badge bg-{{ $s->status === 'completed' ? 'success' : 'secondary' }}">{{ ucfirst($s->status) }}</span></td>
                <td>
                    <a href="{{ route('admin.sales.show', $s) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                    @if($s->status === "completed")
                    <form action="{{ route('admin.sales.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this sale and restore stock?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No sales found.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="card-footer">{{ $sales->links() }}</div>
</div>
@endsection
