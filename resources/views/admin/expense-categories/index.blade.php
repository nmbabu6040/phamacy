@extends("layouts.admin")
@section("title", "Expense Categories")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Expense Categories</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Category</button>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Name</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($expense_categorys as $row)
            <tr><td>{{ $loop->iteration }}</td><td>{{ $row->name }}</td>
            <td><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}"><i class="bi bi-pencil"></i></button>
            <form action="{{ route('admin.expense-categories.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
            <div class="modal fade" id="editModal{{ $row->id }}"><div class="modal-dialog"><div class="modal-content">
            <form action="{{ route('admin.expense-categories.update', $row) }}" method="POST">@csrf @method("PUT")
            <div class="modal-header"><h6 class="modal-title">Edit Category</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ $row->name }}" required></div>
            <div class="modal-footer"><button class="btn btn-primary">Update</button></div></form></div></div></div>
        @empty
            <tr><td colspan="3" class="text-center text-muted py-4">No expense categories found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer">{{ $expense_categorys->links() }}</div>
</div>
<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.expense-categories.store') }}" method="POST">@csrf
    <div class="modal-header"><h6 class="modal-title">Add Expense Category</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div></form>
</div></div></div>
@endsection
