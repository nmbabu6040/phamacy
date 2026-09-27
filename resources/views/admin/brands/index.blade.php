@extends('layouts.admin')
@section('title', 'Brands')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Brands / Manufacturers</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add
            Brand</button>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Logo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->name }}</td>
                            <td>
                                @if ($row->logo)
                                    <img src="{{ asset($row->logo_url) }}" alt="{{ $row->name }}" class="img-fluid"
                                        width="80">
                                @endif
                            </td>
                            <td><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $row->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.brands.destroy', $row) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button
                                        class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        <div class="modal fade" id="editModal{{ $row->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.brands.update', $row) }}" method="POST"
                                        enctype="multipart/form-data">@csrf @method('PUT')
                                        <div class="modal-header">
                                            <h6 class="modal-title">Edit Brand</h6>
                                            <button class="btn-close"data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <label class="form-label">Name</label>
                                                <input name="name"class="form-control mb-2" value="{{ $row->name }}"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="form-label">Logo</label>
                                                <input type="file" name="logo" class="form-control" accept="image/*"
                                                    value="{{ $row->logo }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No brands found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $brands->links() }}</div>
    </div>
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">@csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Add Brand</h6><button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body"><label class="form-label">Name</label><input name="name"
                            class="form-control mb-2" required>
                        <label class="form-label">Logo</label><input type="file" name="logo" class="form-control">
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection
