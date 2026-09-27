@extends("layouts.admin")
@section("title", "Branches")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Branches</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Branch</button>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Code</th><th>Phone</th><th>Staff</th><th>Sales</th><th>Purchases</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($branches as $row)
            <tr>
                <td>{{ $row->name }} @if($row->is_main)<span class="badge bg-primary">Main</span>@endif</td>
                <td>{{ $row->code }}</td><td>{{ $row->phone }}</td>
                <td>{{ $row->users_count }}</td><td>{{ $row->sales_count }}</td><td>{{ $row->purchases_count }}</td>
                <td><span class="badge bg-{{ $row->status ? 'success' : 'secondary' }}">{{ $row->status ? "Active" : "Inactive" }}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}"><i class="bi bi-pencil"></i></button>
                    @if(!$row->is_main)
                    <form action="{{ route('admin.branches.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this branch?')">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                    @endif
                </td>
            </tr>
            <div class="modal fade" id="editModal{{ $row->id }}"><div class="modal-dialog"><div class="modal-content">
                <form action="{{ route('admin.branches.update', $row) }}" method="POST">@csrf @method("PUT")
                <div class="modal-header"><h6 class="modal-title">Edit Branch</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label">Name</label><input name="name" class="form-control mb-2" value="{{ $row->name }}" required>
                    <label class="form-label">Code</label><input name="code" class="form-control mb-2" value="{{ $row->code }}" required>
                    <label class="form-label">Phone</label><input name="phone" class="form-control mb-2" value="{{ $row->phone }}">
                    <label class="form-label">Email</label><input name="email" class="form-control mb-2" value="{{ $row->email }}">
                    <label class="form-label">Address</label><textarea name="address" class="form-control mb-2">{{ $row->address }}</textarea>
                    <div class="form-check"><input type="checkbox" name="is_main" value="1" class="form-check-input" {{ $row->is_main ? 'checked' : '' }}><label class="form-check-label">Main Branch</label></div>
                    <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" {{ $row->status ? 'checked' : '' }}><label class="form-check-label">Active</label></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Update</button></div></form>
            </div></div></div>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No branches found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.branches.store') }}" method="POST">@csrf
    <div class="modal-header"><h6 class="modal-title">Add Branch</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Name</label><input name="name" class="form-control mb-2" required>
        <label class="form-label">Code</label><input name="code" class="form-control mb-2" placeholder="e.g. MIRPUR" required>
        <label class="form-label">Phone</label><input name="phone" class="form-control mb-2">
        <label class="form-label">Email</label><input name="email" class="form-control mb-2">
        <label class="form-label">Address</label><textarea name="address" class="form-control"></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div></form>
</div></div></div>
@endsection
