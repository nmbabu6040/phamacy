@extends("layouts.admin")
@section("title", "Generic Names")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Generic Names (Medicine)</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Generic</button>
</div>
<div class="card">
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Name</th><th>Products</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($generics as $row)
            <tr>
                <td>{{ $loop->iteration }}</td><td>{{ $row->name }}</td><td>{{ $row->products()->count() }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}"><i class="bi bi-pencil"></i></button>
                    <form action="{{ route('admin.generics.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
            <div class="modal fade" id="editModal{{ $row->id }}"><div class="modal-dialog"><div class="modal-content">
                <form action="{{ route('admin.generics.update', $row) }}" method="POST">@csrf @method("PUT")
                <div class="modal-header"><h6 class="modal-title">Edit Generic</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><label class="form-label">Name</label><input type="text" name="name" class="form-control mb-2" value="{{ $row->name }}" required>
                <label class="form-label">Description</label><textarea name="description" class="form-control">{{ $row->description }}</textarea></div>
                <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
                </form>
            </div></div></div>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No generic names found.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="card-footer">{{ $generics->links() }}</div>
</div>
<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.generics.store') }}" method="POST">@csrf
    <div class="modal-header"><h6 class="modal-title">Add Generic Name</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><label class="form-label">Name</label><input type="text" name="name" class="form-control mb-2" required>
    <label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
@endsection
