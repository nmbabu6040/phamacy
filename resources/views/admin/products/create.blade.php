@extends("layouts.admin")
@section("title", "Add Product")
@section("content")
<div class="card">
    <div class="card-header fw-bold">Add New Product</div>
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include("admin.products._form")
            <div class="mt-4"><button class="btn btn-primary">Save Product</button> <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a></div>
        </form>
    </div>
</div>
@endsection
