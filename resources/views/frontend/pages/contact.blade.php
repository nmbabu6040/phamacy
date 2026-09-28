@extends('layouts.frontend')
@section('title', 'Contact Us | ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('content')
    <div class="page-banner">
        <div class="container">
            <h2 data-aos="fade-up">Contact Us</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Contact</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row g-4 mb-5">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="contact-info-card"><i class="bi bi-geo-alt"></i>
                        <h6>Address</h6>
                        <p>{{ $siteSettings['address'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-info-card"><i class="bi bi-telephone"></i>
                        <h6>Phone</h6>
                        <p>{{ $siteSettings['phone'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-info-card"><i class="bi bi-envelope"></i>
                        <h6>Email</h6>
                        <p>{{ $siteSettings['email'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <h4 class="mb-3">Send us a message</h4>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please fix the following:</strong>

                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your Email"
                                    value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control" placeholder="Subject"
                                    value="{{ old('subject') }}">
                            </div>
                            <div class="col-12">
                                <textarea name="message" rows="5" class="form-control" placeholder="Your Message" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12"><button class="btn btn-primary btn-lg">Send Message</button></div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <iframe
                        src="{{ $siteSettings['map_embed_url'] ?? 'https://www.google.com/maps?q=Dhaka,Bangladesh&output=embed' }}"
                        class="w-100 h-100 rounded-4 shadow-sm" style="min-height:350px;border:0" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
