@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')

    <div class="container-fluid py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">Newsletter Subscribers</h4>

                <p class="text-muted mb-0">
                    Manage website newsletter subscribers.
                </p>
            </div>

        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif

        {{-- Search --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <form method="GET" action="{{ route('admin.newsletter-subscribers.index') }}">

                    <div class="row g-2">

                        <div class="col-md-8">

                            <input type="email" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Search subscriber email...">

                        </div>

                        <div class="col-md-4 d-flex gap-2">

                            <button type="submit" class="btn btn-primary flex-fill">

                                <i class="bi bi-search me-1"></i>
                                Search

                            </button>

                            <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">

                                <i class="bi bi-arrow-clockwise"></i>

                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        {{-- Subscribers --}}
        <div class="card border-0 shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="ps-4">#</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Subscribed At</th>
                                <th>Created</th>
                                <th class="text-end pe-4">Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($subscribers as $subscriber)
                                <tr>

                                    <td class="ps-4">
                                        {{ $subscribers->firstItem() + $loop->index }}
                                    </td>

                                    <td>

                                        <a href="mailto:{{ $subscriber->email }}" class="text-decoration-none">

                                            {{ $subscriber->email }}

                                        </a>

                                    </td>

                                    <td>

                                        @if ($subscriber->status)
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Inactive
                                            </span>
                                        @endif

                                    </td>

                                    <td>

                                        @if ($subscriber->subscribed_at)
                                            {{ $subscriber->subscribed_at->format('d M Y, h:i A') }}
                                        @else
                                            —
                                        @endif

                                    </td>

                                    <td>

                                        <small class="text-muted">
                                            {{ $subscriber->created_at->format('d M Y') }}
                                        </small>

                                    </td>

                                    <td class="text-end pe-4">

                                        {{-- Toggle Status --}}
                                        <form method="POST"
                                            action="{{ route('admin.newsletter-subscribers.update', $subscriber) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="status" value="{{ $subscriber->status ? 0 : 1 }}">

                                            <button type="submit"
                                                class="btn btn-sm {{ $subscriber->status ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $subscriber->status ? 'Disable' : 'Activate' }}">

                                                <i
                                                    class="bi {{ $subscriber->status ? 'bi-pause-circle' : 'bi-check-circle' }}"></i>

                                            </button>

                                        </form>

                                        {{-- Delete --}}
                                        <form method="POST"
                                            action="{{ route('admin.newsletter-subscribers.destroy', $subscriber) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this subscriber?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        <i class="bi bi-envelope-paper display-5 text-muted"></i>

                                        <h6 class="mt-3">
                                            No subscribers found
                                        </h6>

                                        <p class="text-muted mb-0">
                                            Newsletter subscribers will appear here.
                                        </p>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            @if ($subscribers->hasPages())
                <div class="card-footer bg-white">

                    {{ $subscribers->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
