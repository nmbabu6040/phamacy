@extends("layouts.admin")
@section("title", "Roles & Permissions")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Roles &amp; Permissions</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Role</button>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Role</th><th>Description</th><th>Staff</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($roles as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><span class="badge bg-primary">{{ $row->name }}</span></td>
                <td>{{ $row->description }}</td>
                <td>{{ $row->users_count }}</td>
                <td>
                    <a href="{{ route('admin.roles.permissions', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-shield-check"></i> Permissions</a>
                    @if($row->slug !== "admin")
                    <form action="{{ route('admin.roles.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No roles found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.roles.store') }}" method="POST">@csrf
    <div class="modal-header"><h6 class="modal-title">Add Role</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Role Name</label><input name="name" class="form-control mb-2" required>
        <label class="form-label">Description</label><textarea name="description" class="form-control"></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div></form>
</div></div></div>
@endsection
