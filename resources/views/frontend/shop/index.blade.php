@extends("layouts.frontend")
@section("title", "Shop | " . ($siteSettings["site_name"] ?? config("app.name")))

@section("content")
<div class="page-banner">
    <div class="container">
        <h2 data-aos="fade-up">Shop Medicines</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Shop</li></ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="shop-sidebar" data-aos="fade-right">
                    <form method="GET">
                        <div class="mb-4">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search medicine...">
                        </div>
                        <h6 class="mb-3">Categories</h6>
                        <ul class="filter-list mb-4">
                            <li><a href="{{ route('shop.index') }}" class="{{ !request('category') ? 'active' : '' }}">All Categories</a></li>
                            @foreach($categories as $c)
                                <li><a href="{{ route('shop.index', ['category'=>$c->slug]) }}" class="{{ request('category')===$c->slug ? 'active' : '' }}">{{ $c->name }}</a></li>
                            @endforeach
                        </ul>
                        <h6 class="mb-3">Generic Name</h6>
                        <ul class="filter-list">
                            <li><a href="{{ route('shop.index') }}" class="{{ !request('generic') ? 'active' : '' }}">All Generics</a></li>
                            @foreach($generics->take(8) as $g)
                                <li><a href="{{ route('shop.index', ['generic'=>$g->slug]) }}" class="{{ request('generic')===$g->slug ? 'active' : '' }}">{{ $g->name }}</a></li>
                            @endforeach
                        </ul>
                    </form>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
                    <span class="text-muted">{{ $products->total() }} products found</span>
                    <form method="GET" class="d-flex align-items-center gap-2">
                        @foreach(request()->except("sort") as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                        <label class="text-muted small mb-0">Sort:</label>
                        <select name="sort" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                            <option value="">Newest</option>
                            <option value="price_low" @selected(request('sort')==='price_low')>Price: Low to High</option>
                            <option value="price_high" @selected(request('sort')==='price_high')>Price: High to Low</option>
                        </select>
                    </form>
                </div>

                <div class="row g-4">
                    @forelse($products as $p)
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 40 }}">
                            <div class="product-card">
                                <div class="product-thumb">
                                    <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/300x220?text='.urlencode($p->name) }}" alt="{{ $p->name }}">
                                    @if($p->isLowStock())<span class="stock-badge">Low Stock</span>@endif
                                    <a href="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/600' }}" class="quick-view venobox" data-gall="shop-products" title="{{ $p->name }}"><i class="bi bi-eye"></i></a>
                                </div>
                                <div class="product-body">
                                    <span class="text-muted small">{{ $p->generic->name ?? "" }} · {{ $p->strength }}</span>
                                    <h6 class="mb-1"><a href="{{ route('shop.show', $p) }}">{{ $p->name }}</a></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary">৳{{ number_format($p->sale_price,2) }}</span>
                                        <a href="{{ route('shop.show', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted py-5">No products matched your search.</p>
                    @endforelse
                </div>

                <div class="mt-4">{{ $products->links() }}</div>
            </div>
        </div>
    </div>
</section>
@endsection
