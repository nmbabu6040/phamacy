@extends('layouts.admin')
@section('title', 'Add FAQ')

@section('content')
    <div class="card">
        <div class="card-header fw-bold">Add FAQ</div>
        <div class="card-body">
            <form action="{{ route('admin.faqs.store') }}" method="POST">
                @csrf
                @include('admin.faqs._form')
                <button class="btn btn-primary mt-3">Save FAQ</button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-light mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
