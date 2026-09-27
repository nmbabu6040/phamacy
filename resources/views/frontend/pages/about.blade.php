@extends('layouts.frontend')
@section('title', 'About Us | ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('content')
    <div class="page-banner">
        <div class="container">
            <h2 data-aos="fade-up">About Us</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">About</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="{{ !empty($siteSettings['about_image']) ? Storage::url($siteSettings['about_image']) : 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800' }}"
                        class="img-fluid rounded-4 shadow">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span
                        class="badge bg-primary-subtle text-primary mb-2">{{ $siteSettings['about_badge'] ?? 'About ' . ($siteSettings['site_name'] ?? config('app.name')) }}</span>
                    <h2 class="mb-3">{{ $siteSettings['about_heading'] ?? 'Committed to Your Health & Wellbeing' }}</h2>
                    <p>{{ $siteSettings['about_description'] ?? 'We are a full-service pharmacy providing genuine medicines, expert pharmacist consultation, and fast home delivery.' }}
                    </p>
                    <div class="row g-3 mt-2">
                        @foreach ($aboutFeatures as $f)
                            <div class="col-6"><i class="bi bi-check-circle-fill text-primary"></i> {{ $f->text }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row text-center g-4">
                @foreach ($aboutValues as $v)
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="value-card"><i class="bi {{ $v->icon }}"></i>
                            <h5>{{ $v->title }}</h5>
                            <p>{{ $v->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
