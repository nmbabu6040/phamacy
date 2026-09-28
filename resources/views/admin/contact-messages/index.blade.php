@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')

    <div class="container-fluid py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Contact Messages</h4>
                <p class="text-muted mb-0">
                    Messages received from the website contact form.
                </p>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Search & Filter --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <form method="GET" action="{{ route('admin.contact-messages.index') }}">

                    <div class="row g-2">

                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Search name, email or subject...">
                        </div>

                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>

                                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>
                                    New
                                </option>

                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>
                                    Read
                                </option>

                                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>
                                    Replied
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">

                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search me-1"></i>
                                Search
                            </button>

                            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>

                        </div>

                    </div>

                </form>

            </div>
        </div>

        {{-- Messages Table --}}
        <div class="card border-0 shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="ps-4">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($messages as $message)
                                <tr>

                                    <td class="ps-4">
                                        {{ $messages->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $message->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        <a href="mailto:{{ $message->email }}" class="text-decoration-none">
                                            {{ $message->email }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $message->subject ?: '—' }}
                                    </td>

                                    <td>
                                        {{ \Illuminate\Support\Str::limit($message->message, 50) }}
                                    </td>

                                    <td>

                                        @if ($message->status === 'new')
                                            <span class="badge bg-danger">
                                                New
                                            </span>
                                        @elseif($message->status === 'read')
                                            <span class="badge bg-warning text-dark">
                                                Read
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Replied
                                            </span>
                                        @endif

                                    </td>

                                    <td>
                                        <small class="text-muted">
                                            {{ $message->created_at->format('d M Y, h:i A') }}
                                        </small>
                                    </td>

                                    <td class="text-end pe-4">

                                        <a href="{{ route('admin.contact-messages.show', $message) }}"
                                            class="btn btn-sm btn-outline-primary" title="View Message">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <form action="{{ route('admin.contact-messages.destroy', $message) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this message?');">

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

                                    <td colspan="8" class="text-center py-5">

                                        <i class="bi bi-envelope-open display-5 text-muted"></i>

                                        <h6 class="mt-3">
                                            No contact messages found
                                        </h6>

                                        <p class="text-muted mb-0">
                                            Website messages will appear here.
                                        </p>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            @if ($messages->hasPages())
                <div class="card-footer bg-white">
                    {{ $messages->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection
