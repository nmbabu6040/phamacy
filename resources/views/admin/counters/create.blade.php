@extends('layouts.admin')
@section('title', 'Add Counter')

@section('content')
    <div class="card">
        <div class="card-header fw-bold">Add Counter</div>
        <div class="card-body">
            <form action="{{ route('admin.counters.store') }}" method="POST">
                @csrf
                @include('admin.counters._form')
                <button class="btn btn-primary mt-3">Save Counter</button>
                <a href="{{ route('admin.counters.index') }}" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
