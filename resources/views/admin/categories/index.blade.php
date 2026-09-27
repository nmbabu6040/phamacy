@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Categories</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add
            Category</button>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorys as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->products()->count() }}</td>
                            <td>
                                <img src="{{ $row->image_url }}" alt="{{ $row->name }}" class="img-fluid" width="80">
                            </td>
                            <td><span
                                    class="badge bg-{{ $row->status ? 'success' : 'secondary' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $row->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.categories.destroy', $row) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button
                                        class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        <div class="modal fade" id="editModal{{ $row->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.categories.update', $row) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h6 class="modal-title">Edit Category</h6><button class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control mb-2"
                                                value="{{ $row->name }}" required>

                                            <label class="form-label">Image</label>
                                            <input type="file" name="image" class="form-control mb-2" accept="image/*">
                                            <div class="form-check"><input type="checkbox" name="status" value="1"
                                                    class="form-check-input" {{ $row->status ? 'checked' : '' }}><label
                                                    class="form-check-label">Active</label></div>
                                        </div>
                                        <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $categorys->links() }}</div>
    </div>
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">@csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Add Category</h6><button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Name</label><input type="text" name="name" class="form-control mb-2"
                            required>
                        <label class="form-label">Image</label><input type="file" name="image" class="form-control">
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection
