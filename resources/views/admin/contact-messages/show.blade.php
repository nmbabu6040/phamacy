@extends('layouts.admin')

@section('title', 'View Contact Message')

@section('content')

    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">Contact Message</h4>
                <p class="text-muted mb-0">
                    View and manage customer message.
                </p>
            </div>

            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back to Messages

            </a>

        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif

        <div class="row g-4">

            {{-- Message --}}
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0">
                            <i class="bi bi-envelope me-2"></i>
                            Message Details
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-4">

                            <label class="text-muted small">
                                Name
                            </label>

                            <div class="fw-semibold">
                                {{ $contactMessage->name }}
                            </div>

                        </div>

                        <div class="mb-4">

                            <label class="text-muted small">
                                Email
                            </label>

                            <div>

                                <a href="mailto:{{ $contactMessage->email }}">
                                    {{ $contactMessage->email }}
                                </a>

                            </div>

                        </div>

                        <div class="mb-4">

                            <label class="text-muted small">
                                Subject
                            </label>

                            <div class="fw-semibold">
                                {{ $contactMessage->subject ?: 'No subject' }}
                            </div>

                        </div>

                        <div class="mb-0">

                            <label class="text-muted small">
                                Message
                            </label>

                            <div class="bg-light rounded-3 p-3" style="white-space: pre-line;">

                                {{ $contactMessage->message }}

                            </div>

                        </div>

                    </div>

                    <div class="card-footer bg-white">

                        <small class="text-muted">

                            Received:
                            {{ $contactMessage->created_at->format('d M Y, h:i A') }}

                        </small>

                    </div>

                </div>

            </div>

            {{-- Status --}}
            <div class="col-lg-4">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0">
                            <i class="bi bi-gear me-2"></i>
                            Message Status
                        </h5>

                    </div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('admin.contact-messages.update', $contactMessage) }}">

                            @csrf
                            @method('PUT')

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status" class="form-select mb-3">

                                <option value="new" {{ $contactMessage->status === 'new' ? 'selected' : '' }}>
                                    New
                                </option>

                                <option value="read" {{ $contactMessage->status === 'read' ? 'selected' : '' }}>
                                    Read
                                </option>

                                <option value="replied" {{ $contactMessage->status === 'replied' ? 'selected' : '' }}>
                                    Replied
                                </option>

                            </select>

                            <button type="submit" class="btn btn-primary w-100">

                                <i class="bi bi-check2-circle me-1"></i>
                                Update Status

                            </button>

                        </form>

                        <hr>

                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}"
                            onsubmit="return confirm('Are you sure you want to delete this message?');">

                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-outline-danger w-100">

                                <i class="bi bi-trash me-1"></i>
                                Delete Message

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
