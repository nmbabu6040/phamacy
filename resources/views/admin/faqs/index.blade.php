@extends('layouts.admin')
@section('title', 'FAQs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Frequently Asked Questions</h4>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add FAQ</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mb-3" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search question..."
                    value="{{ request('search') }}">
            </form>

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $f)
                        <tr>
                            <td>{{ $f->question }}</td>
                            <td>{{ $f->sort_order }}</td>
                            <td>
                                @if ($f->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.faqs.edit', $f) }}" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.faqs.destroy', $f) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this FAQ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No FAQs yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $faqs->links() }}
        </div>
    </div>
@endsection
