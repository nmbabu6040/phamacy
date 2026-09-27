@extends('layouts.admin')
@section('title', 'Edit Product')
@section('content')
    <div class="card">
        <div class="card-header fw-bold">Edit Product — {{ $product->name }}</div>
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('admin.products._form')
                <div class="mt-4"><button class="btn btn-primary">Update Product</button> <a
                        href="{{ route('admin.products.index') }}" class="btn btn-light">Back</a></div>
            </form>
        </div>
    </div>
@endsection
