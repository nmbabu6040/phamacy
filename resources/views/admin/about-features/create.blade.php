@extends('layouts.admin')
@section('title', 'Add Feature')
@section('content')
    <div class="card">
        <div class="card-header fw-bold">Add Feature</div>
        <div class="card-body">
            <form action="{{ route('admin.about-features.store') }}" method="POST">
                @csrf
                @include('admin.about-features._form')
                <button class="btn btn-primary mt-3">Save Feature</button>
                <a href="{{ route('admin.about-features.index') }}" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
