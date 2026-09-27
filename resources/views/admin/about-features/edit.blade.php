@extends('layouts.admin')
@section('title', 'Edit Feature')
@section('content')
    <div class="card">
        <div class="card-header fw-bold">Edit Feature</div>
        <div class="card-body">
            <form action="{{ route('admin.about-features.update', $item) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.about-features._form')
                <button class="btn btn-primary mt-3">Update Feature</button>
                <a href="{{ route('admin.about-features.index') }}" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
