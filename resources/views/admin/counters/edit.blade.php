@extends('layouts.admin')
@section('title', 'Edit Counter')

@section('content')
    <div class="card">
        <div class="card-header fw-bold">Edit Counter</div>
        <div class="card-body">
            <form action="{{ route('admin.counters.update', $counter) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.counters._form')
                <button class="btn btn-primary mt-3">Update Counter</button>
                <a href="{{ route('admin.counters.index') }}" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
