@extends('layouts.admin')
@section('title', 'About Page Features')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">About Page — Feature Checklist</h4>
        <a href="{{ route('admin.about-features.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add
            Feature</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Text</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i)
                        <tr>
                            <td><i class="bi bi-check-circle-fill text-primary"></i> {{ $i->text }}</td>
                            <td>{{ $i->sort_order }}</td>
                            <td>
                                @if ($i->status)
                                    <span class="badge bg-success">Active</span>
                                @else<span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.about-features.edit', $i) }}"
                                    class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.about-features.destroy', $i) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Delete this feature?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No features yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $items->links() }}
        </div>
    </div>
@endsection
