@extends('layouts.admin')
@section('title', 'Add Value Card')
@section('content')
    <div class="card">
        <div class="card-header fw-bold">Add Value Card</div>
        <div class="card-body">
            <form action="{{ route('admin.about-values.store') }}" method="POST">
                @csrf
                @include('admin.about-values._form')
                <button class="btn btn-primary mt-3">Save Value Card</button>
                <a href="{{ route('admin.about-values.index') }}" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
