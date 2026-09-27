@extends('layouts.admin')
@section('title', 'Counters')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Homepage Counters</h4>
        <a href="{{ route('admin.counters.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Counter</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mb-3" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search label..."
                    value="{{ request('search') }}">
            </form>

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Count</th>
                        <th>Label</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($counters as $c)
                        <tr>
                            <td><i class="bi {{ $c->icon }} fs-4"></i></td>
                            <td>{{ $c->count }}</td>
                            <td>{{ $c->label }}</td>
                            <td>{{ $c->sort_order }}</td>
                            <td>
                                @if ($c->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.counters.edit', $c) }}" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.counters.destroy', $c) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this counter?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No counters yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $counters->links() }}
        </div>
    </div>
@endsection
