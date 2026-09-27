@extends('layouts.admin')
@section('title', 'Homepage Sliders')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Homepage Sliders</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add
            Slide</button>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sliders as $row)
                        <tr>
                            <td><img src="{{ $row->image_url }}" width="80" class="rounded"
                                    onerror="this.src="https://via.placeholder.com/80?text=No+Image"></td>
                            <td>{{ $row->title }}</td>
                            <td>{{ $row->sort_order }}</td>
                            <td><span
                                    class="badge bg-{{ $row->status ? 'success' : 'secondary' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span>
                            </td>
                            {{-- Actions --}}
                            <td>

                                {{-- Edit --}}
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $row->id }}" title="Edit Slider">
                                    <i class="bi bi-pencil"></i>
                                </button>


                                {{-- Delete --}}
                                <form action="{{ route('admin.sliders.destroy', $row) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this slider?');">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Slider">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No slides yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $sliders->links() }}</div>
    </div>

    {{-- edit model  --}}

    @foreach ($sliders as $row)
        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form action="{{ route('admin.sliders.update', $row) }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')


                        {{-- Header --}}
                        <div class="modal-header">

                            <h5 class="modal-title">
                                <i class="bi bi-pencil-square me-1"></i>
                                Edit Slider
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                        </div>


                        {{-- Body --}}
                        <div class="modal-body">

                            <div class="row">


                                {{-- Title --}}
                                <div class="col-md-12 mb-3">

                                    <label class="form-label">
                                        Title
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $row->title) }}" required maxlength="255">

                                </div>


                                {{-- Subtitle --}}
                                <div class="col-md-12 mb-3">

                                    <label class="form-label">
                                        Subtitle
                                    </label>

                                    <textarea name="subtitle" class="form-control" rows="3" maxlength="1000">{{ old('subtitle', $row->subtitle) }}</textarea>

                                </div>


                                {{-- Button Text --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Button Text
                                    </label>

                                    <input type="text" name="button_text" class="form-control"
                                        value="{{ old('button_text', $row->button_text) }}" maxlength="100">

                                </div>


                                {{-- Button Link --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Button Link
                                    </label>

                                    <input type="text" name="button_link" class="form-control"
                                        value="{{ old('button_link', $row->button_link) }}" maxlength="500"
                                        placeholder="https://example.com">

                                </div>


                                {{-- Current Image --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Current Image
                                    </label>

                                    @if ($row->image)
                                        <div>

                                            <img src="{{ Storage::url($row->image) }}" alt="{{ $row->title }}"
                                                class="img-thumbnail"
                                                style="max-width: 220px; max-height: 120px; object-fit: cover;">

                                        </div>
                                    @else
                                        <div class="text-muted">
                                            No image uploaded.
                                        </div>
                                    @endif

                                </div>


                                {{-- New Image --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Change Image
                                    </label>

                                    <input type="file" name="image" class="form-control"
                                        accept=".jpg,.jpeg,.png,.webp">

                                    <div class="form-text">
                                        JPG, JPEG, PNG or WEBP. Maximum 2MB.
                                    </div>

                                </div>


                                {{-- Sort Order --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Sort Order
                                    </label>

                                    <input type="number" name="sort_order" class="form-control" min="0"
                                        value="{{ old('sort_order', $row->sort_order ?? 0) }}">

                                </div>


                                {{-- Status --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select name="status" class="form-select">

                                        <option value="1" {{ $row->status ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="0" {{ !$row->status ? 'selected' : '' }}>
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>


                        {{-- Footer --}}
                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>
                                Update Slider
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    @endforeach

    {{-- add slider model  --}}
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">@csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Add Slide</h6><button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Title</label><input name="title" class="form-control mb-2" required>
                        <label class="form-label">Subtitle</label><input name="subtitle" class="form-control mb-2">
                        <label class="form-label">Button Text</label><input name="button_text" class="form-control mb-2">
                        <label class="form-label">Button Link</label><input name="button_link" class="form-control mb-2">
                        <label class="form-label">Image</label><input type="file" name="image"
                            class="form-control mb-2">
                        <label class="form-label">Sort Order</label><input type="number" name="sort_order"
                            class="form-control" value="0">
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection
