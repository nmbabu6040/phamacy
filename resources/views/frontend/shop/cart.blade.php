@extends("layouts.frontend")
@section("title", "Your Cart")
@section("content")
<div class="page-banner"><div class="container"><h2 data-aos="fade-up">Your Cart</h2>
<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Cart</li></ol></nav></div></div>

<section class="py-5">
    <div class="container">
        @if(session("success"))<div class="alert alert-success">{{ session("success") }}</div>@endif
        @if(session("error"))<div class="alert alert-danger">{{ session("error") }}</div>@endif

        @if(count($items))
        <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
            @foreach($items as $row)
                <tr>
                    <td>{{ $row["product"]->name }}</td>
                    <td>৳{{ number_format($row["product"]->sale_price,2) }}</td>
                    <td style="width:140px">
                        <form action="{{ route('cart.update', $row["product"]) }}" method="POST" class="d-flex">
                            @csrf @method("PATCH")
                            <input type="number" name="quantity" value="{{ $row["qty"] }}" min="1" max="{{ $row["product"]->stock_qty }}" class="form-control form-control-sm me-1">
                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button>
                        </form>
                    </td>
                    <td>৳{{ number_format($row["line_total"],2) }}</td>
                    <td>
                        <form action="{{ route('cart.remove', $row["product"]) }}" method="POST" onsubmit="return confirm('Remove item?')">
                            @csrf @method("DELETE")
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <div class="d-flex justify-content-end">
            <div class="text-end">
                <h5>Subtotal: ৳{{ number_format($subtotal,2) }}</h5>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg mt-2"><i class="bi bi-bag-check"></i> Proceed to Checkout</a>
            </div>
        </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <h5 class="mt-3">Your cart is empty</h5>
                <a href="{{ route('shop.index') }}" class="btn btn-primary mt-2">Browse Medicines</a>
            </div>
        @endif
    </div>
</section>
@endsection
