@extends("layouts.admin")
@section("title", "Suppliers")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Suppliers</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Supplier</button>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Name</th><th>Company</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($suppliers as $row)
            <tr><td>{{ $loop->iteration }}</td><td>{{ $row->name }}</td><td>{{ $row->company_name }}</td><td>{{ $row->phone }}</td><td>{{ $row->email }}</td>
            <td><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}"><i class="bi bi-pencil"></i></button>
            <form action="{{ route('admin.suppliers.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
            <div class="modal fade" id="editModal{{ $row->id }}"><div class="modal-dialog"><div class="modal-content">
            <form action="{{ route('admin.suppliers.update', $row) }}" method="POST">@csrf @method("PUT")
            <div class="modal-header"><h6 class="modal-title">Edit Supplier</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label">Name</label><input name="name" class="form-control mb-2" value="{{ $row->name }}" required>
                <label class="form-label">Company</label><input name="company_name" class="form-control mb-2" value="{{ $row->company_name }}">
                <label class="form-label">Phone</label><input name="phone" class="form-control mb-2" value="{{ $row->phone }}">
                <label class="form-label">Email</label><input name="email" class="form-control mb-2" value="{{ $row->email }}">
                <label class="form-label">Address</label><textarea name="address" class="form-control">{{ $row->address }}</textarea>
            </div>
            <div class="modal-footer"><button class="btn btn-primary">Update</button></div></form></div></div></div>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No suppliers found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $suppliers->links() }}</div>
</div>
<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.suppliers.store') }}" method="POST">@csrf
    <div class="modal-header"><h6 class="modal-title">Add Supplier</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Name</label><input name="name" class="form-control mb-2" required>
        <label class="form-label">Company</label><input name="company_name" class="form-control mb-2">
        <label class="form-label">Phone</label><input name="phone" class="form-control mb-2">
        <label class="form-label">Email</label><input name="email" class="form-control mb-2">
        <label class="form-label">Address</label><textarea name="address" class="form-control"></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div></form>
</div></div></div>
@endsection
