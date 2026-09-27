@extends('layouts.admin')
@section('title', 'Edit FAQ')

@section('content')
    <div class="card">
        <div class="card-header fw-bold">Edit FAQ</div>
        <div class="card-body">
            <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.faqs._form')
                <button class="btn btn-primary mt-3">Update FAQ</button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
