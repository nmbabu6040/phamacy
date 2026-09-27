@extends("layouts.admin")
@section("title", "Activity Log")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Activity Log</h4>
    <form action="{{ route('admin.activity-logs.clear') }}" method="POST" onsubmit="return confirm('Clear ALL activity logs?')">@csrf @method("DELETE")
        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Clear All</button>
    </form>
</div>
<div class="card mb-3"><div class="card-body"><form class="row g-2" method="GET">
    <div class="col-md-3"><select name="module" class="form-select"><option value="">All Modules</option>@foreach($modules as $m)<option value="{{ $m }}" @selected(request('module')==$m)>{{ $m }}</option>@endforeach</select></div>
    <div class="col-md-3"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
    <div class="col-md-3"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
    <div class="col-md-3"><button class="btn btn-outline-primary w-100">Filter</button></div>
</form></div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>IP</th><th>Date</th><th></th></tr></thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                <td>{{ $log->user->name ?? "System" }}</td>
                <td><span class="badge bg-{{ $log->action==='deleted' ? 'danger' : ($log->action==='created' ? 'success' : 'secondary') }}">{{ ucfirst($log->action) }}</span></td>
                <td>{{ $log->module }}</td>
                <td>{{ $log->description }}</td>
                <td class="text-muted small">{{ $log->ip_address }}</td>
                <td class="text-muted small">{{ $log->created_at->format("d M Y, h:i A") }}</td>
                <td><form action="{{ route('admin.activity-logs.destroy', $log) }}" method="POST">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button></form></td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No activity recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $logs->links() }}</div>
</div>
@endsection
