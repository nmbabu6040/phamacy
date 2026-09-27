@extends('layouts.frontend')
@section('title', ($siteSettings['meta_title'] ?? config('app.name')) . ' - Home')

@section('content')

    <!-- Hero Slider -->
    <section class="hero-slider">
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                @forelse($sliders as $slide)
                    <div class="swiper-slide"
                        style="background-image:linear-gradient(120deg, rgba(17,24,39,.75), rgba(79,70,229,.55)), url({{ $slide->image_url }})">
                        <div class="container h-100 d-flex align-items-center">
                            <div class="hero-content text-white" data-aos="fade-up">
                                <h1 class="display-4 fw-bold">{{ $slide->title }}</h1>
                                <p class="lead">{{ $slide->subtitle }}</p>
                                @if ($slide->button_text)
                                    <a href="{{ $slide->button_link ?? '#' }}"
                                        class="btn btn-primary btn-lg mt-2">{{ $slide->button_text }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="swiper-slide" style="background:linear-gradient(120deg,#4f46e5,#06b6d4)">
                        <div class="container h-100 d-flex align-items-center">
                            <div class="hero-content text-white" data-aos="fade-up">
                                <h1 class="display-4 fw-bold">Your Health, Our Priority</h1>
                                <p class="lead">Genuine medicines delivered to your doorstep</p>
                                <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg mt-2">Shop Now</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Counters (parallax strip) -->
    <section class="counter-parallax" data-aos="fade-in">
        <div class="container">
            <div class="row text-center text-white g-4">

                @foreach ($counters as $counter)
                    <div class="col-6 col-md-3">
                        <div class="counter-icon"><i class="bi {{ $counter->icon }}"></i></div>
                        <h2 class="counter" data-count="{{ $counter->count }}">0</h2>
                        <p>{{ $counter->label }}</p>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- Category grid (Isotope) -->
    <section class="py-5">
        <div class="container">
            <div class="section-title text-center mb-4" data-aos="fade-up">
                <span class="badge bg-primary-subtle text-primary mb-2">Browse</span>
                <h2>Shop by Category</h2>
            </div>
            <div class="row isotope-grid g-4">
                @foreach ($categories as $cat)
                    <div class="col-lg-3 col-md-4 col-6 isotope-item" data-aos="zoom-in"
                        data-aos-delay="{{ $loop->index * 50 }}">
                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="category-card">
                            @if (!$cat->image)
                                <i class="bi bi-capsule-pill"></i>
                            @else
                                <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="img-fluid"
                                    width="80">
                            @endif

                            <h6>{{ $cat->name }}</h6>
                            <span>{{ $cat->products_count }} items</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured products -->
    <section class="py-5 bg-light-soft">
        <div class="container">
            <div class="section-title text-center mb-4" data-aos="fade-up">
                <span class="badge bg-primary-subtle text-primary mb-2">Featured</span>
                <h2>Popular Medicines</h2>
            </div>
            <div class="row g-4">
                @forelse($featuredProducts as $p)
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                        <div class="product-card">
                            <div class="product-thumb">
                                <img src="{{ $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/300x220?text=' . urlencode($p->name) }}"
                                    alt="{{ $p->name }}">
                                <a href="{{ $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/600' }}"
                                    class="quick-view venobox" data-gall="products" title="{{ $p->name }}"><i
                                        class="bi bi-eye"></i></a>
                            </div>
                            <div class="product-body">
                                <span class="text-muted small">{{ $p->generic->name ?? '' }}</span>
                                <h6 class="mb-1"><a href="{{ route('shop.show', $p) }}">{{ $p->name }}</a></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">৳{{ number_format($p->sale_price, 2) }}</span>
                                    <a href="{{ route('shop.show', $p) }}" class="btn btn-sm btn-outline-primary"><i
                                            class="bi bi-cart-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No featured products yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Why choose us / Accordion FAQ -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="badge bg-primary-subtle text-primary mb-2">FAQ</span>
                    <h2 class="mb-4">Frequently Asked Questions</h2>
                    <div class="accordion" id="faqAccordion">
                        @forelse($faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                        data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="faq{{ $faq->id }}"
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">{{ $faq->answer }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No FAQs added yet.</p>
                        @endforelse
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="{{ !empty($siteSettings['faq_image']) ? Storage::url($siteSettings['faq_image']) : 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=800' }}"
                        class="img-fluid rounded-4 shadow" alt="Pharmacy">
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        new Swiper(".heroSwiper", {
            loop: true,
            autoplay: {
                delay: 4500
            },
            effect: "fade",
            pagination: {
                el: ".swiper-pagination",
                clickable: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
        });
    </script>
@endpush
