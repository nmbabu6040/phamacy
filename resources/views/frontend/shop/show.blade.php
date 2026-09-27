@extends("layouts.frontend")
@section("title", $product->name . " | " . ($siteSettings["site_name"] ?? config("app.name")))

@section("content")
<div class="page-banner">
    <div class="container">
        <h2 data-aos="fade-up">{{ $product->name }}</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/500?text='.urlencode($product->name) }}" class="img-fluid rounded-4 shadow-sm w-100">
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="badge bg-primary-subtle text-primary mb-2">{{ $product->category->name ?? "" }}</span>
                <h2>{{ $product->name }}</h2>
                <p class="text-muted">Generic: <b>{{ $product->generic->name ?? "-" }}</b> &nbsp;|&nbsp; Strength: <b>{{ $product->strength ?? "-" }}</b> &nbsp;|&nbsp; Form: <b>{{ $product->dosage_form }}</b></p>
                <h3 class="text-primary mb-3">৳{{ number_format($product->sale_price,2) }}</h3>

                @if($product->units->count() > 1)
                <div class="table-responsive mb-3" style="max-width:400px">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light"><tr><th>Unit</th><th>Contains</th><th>Price</th></tr></thead>
                        <tbody>
                        @foreach($product->units->sortBy("conversion_factor") as $u)
                            <tr>
                                <td>{{ $u->unit->name ?? "Unit" }}</td>
                                <td>{{ $u->conversion_factor }} pc(s)</td>
                                <td class="fw-bold">৳{{ number_format($u->sale_price,2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <p>{{ $product->description ?: "No additional description provided for this product." }}</p>
                <ul class="list-unstyled mb-4">
                    <li><i class="bi bi-check-circle text-success"></i> Brand: {{ $product->brand->name ?? "-" }}</li>
                    <li><i class="bi bi-check-circle text-success"></i> Availability:
                        @if($product->stock_qty > 0)<span class="text-success">In Stock ({{ $product->stock_qty }} {{ $product->unit->short_name ?? '' }})</span>@else <span class="text-danger">Out of Stock</span>@endif
                    </li>
                    @if($product->expiry_date)<li><i class="bi bi-check-circle text-success"></i> Expiry: {{ $product->expiry_date->format("d M Y") }}</li>@endif
                </ul>
                @if($product->stock_qty > 0)
                <form action="{{ route('cart.add', $product) }}" method="POST" class="d-flex align-items-center gap-2">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_qty }}" class="form-control" style="width:90px">
                    <button class="btn btn-primary btn-lg"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                </form>
                @else
                    <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
                @endif
                <div class="mt-3 small text-muted">
                    <i class="bi bi-upc-scan"></i> Product Code: <b>{{ $product->code }}</b>
                </div>
            </div>
        </div>

        @if($related->count())
        <div class="mt-5">
            <h4 class="mb-4" data-aos="fade-up">Related Products</h4>
            <div class="row g-4">
                @foreach($related as $p)
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index*60 }}">
                        <div class="product-card">
                            <div class="product-thumb"><img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/300x220' }}" alt="{{ $p->name }}"></div>
                            <div class="product-body">
                                <h6><a href="{{ route('shop.show',$p) }}">{{ $p->name }}</a></h6>
                                <span class="fw-bold text-primary">৳{{ number_format($p->sale_price,2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
