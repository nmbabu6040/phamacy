@extends("layouts.admin")
@section("title", "Staff / Users")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Staff / Users</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add User</button>
</div>
<div class="card mb-3"><div class="card-body"><form class="row g-2" method="GET">
    <div class="col-md-10"><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email..."></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
</form></div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($users as $row)
            <tr>
                <td>{{ $row->name }}</td><td>{{ $row->email }}</td>
                <td><span class="badge bg-info text-dark">{{ $row->role->name ?? "-" }}</span></td>
                <td><span class="badge bg-{{ $row->status==='active' ? 'success' : 'secondary' }}">{{ ucfirst($row->status) }}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}"><i class="bi bi-pencil"></i></button>
                    @if($row->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                    @endif
                </td>
            </tr>
            <div class="modal fade" id="editModal{{ $row->id }}"><div class="modal-dialog"><div class="modal-content">
            <form action="{{ route('admin.users.update', $row) }}" method="POST">@csrf @method("PUT")
            <div class="modal-header"><h6 class="modal-title">Edit User</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label">Name</label><input name="name" class="form-control mb-2" value="{{ $row->name }}" required>
                <label class="form-label">Email</label><input type="email" name="email" class="form-control mb-2" value="{{ $row->email }}" required>
                <label class="form-label">Phone</label><input name="phone" class="form-control mb-2" value="{{ $row->phone }}">
                <label class="form-label">Role</label><select name="role_id" class="form-select mb-2" required>@foreach($roles as $r)<option value="{{ $r->id }}" @selected($row->role_id==$r->id)>{{ $r->name }}</option>@endforeach</select>
                <label class="form-label">New Password (optional)</label><input type="password" name="password" class="form-control mb-2">
                <div class="form-check"><input type="checkbox" name="status" value="1" class="form-check-input" {{ $row->status==='active' ? 'checked' : '' }}><label class="form-check-label">Active</label></div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary">Update</button></div></form></div></div></div>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $users->links() }}</div>
</div>
<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.users.store') }}" method="POST">@csrf
    <div class="modal-header"><h6 class="modal-title">Add User</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Name</label><input name="name" class="form-control mb-2" required>
        <label class="form-label">Email</label><input type="email" name="email" class="form-control mb-2" required>
        <label class="form-label">Phone</label><input name="phone" class="form-control mb-2">
        <label class="form-label">Role</label><select name="role_id" class="form-select mb-2" required>@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select>
        <label class="form-label">Password</label><input type="password" name="password" class="form-control mb-2" required>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div></form>
</div></div></div>
@endsection
