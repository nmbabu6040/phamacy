@extends('layouts.admin')
@section('title', 'About Page Values')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">About Page — Value Cards</h4>
        <a href="{{ route('admin.about-values.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Value
            Card</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i)
                        <tr>
                            <td><i class="bi {{ $i->icon }} fs-4"></i></td>
                            <td>{{ $i->title }}</td>
                            <td>{{ $i->sort_order }}</td>
                            <td>
                                @if ($i->status)
                                    <span class="badge bg-success">Active</span>
                                @else<span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.about-values.edit', $i) }}"
                                    class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.about-values.destroy', $i) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this value card?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No value cards yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $items->links() }}
        </div>
    </div>
@endsection
