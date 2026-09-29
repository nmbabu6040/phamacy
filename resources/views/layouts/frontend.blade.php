<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteSettings['meta_title'] ?? config('app.name'))</title>

    <meta name="description" content="@yield('meta_description', $siteSettings['meta_description'] ?? '')">
    <meta name="keywords" content="{{ $siteSettings['meta_keywords'] ?? '' }}">
    <meta property="og:title" content="@yield('title', $siteSettings['meta_title'] ?? config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', $siteSettings['meta_description'] ?? '')">

    <meta property="og:image" content="@yield('og_image', $siteSettings['og_image'] ?? '')">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $siteSettings['meta_title'] ?? config('app.name'))">
    <meta name="twitter:description" content="@yield('meta_description', $siteSettings['meta_description'] ?? '')">
    <meta name="twitter:image" content="@yield('og_image', !empty($siteSettings['og_image']) ? Storage::url($siteSettings['og_image']) : '')">

    <link rel="icon" type="image/x-icon"
        href="{{ !empty($siteSettings['site_favicon']) ? Storage::url($siteSettings['site_favicon']) : asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/venobox/2.2.0/venobox.min.css"
        integrity="sha512-FnujlF1t0thohgebheftAha2ClL1j+K3WmzibQU03M1GIC4ZIgPypoQgQiQAYn/2b7jpch7bm6dWi5O6GucSSg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/14.2.0/swiper-bundle.min.css"
        integrity="sha512-o7Knr4VAyVWuQ+zWMXH8bbv8zhp2EAQQKSMTiM7S6KjoK5MXagmNbVhUdmojS94zr3cCIERxQyEOoqSdSk2bLA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('styles')
</head>

<body>

    <!-- Preloader -->
    <div id="preloader">
        <div class="pill-spinner"></div>
    </div>

    <!-- Top bar -->
    <div class="topbar-strip d-none d-lg-block">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <div class="small">
                <i class="bi bi-telephone"></i> {{ $siteSettings['phone'] ?? '+880 1700-000000' }}
                &nbsp;
                <i class="bi bi-envelope"></i> {{ $siteSettings['email'] ?? '' }}
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="social-links">
                    <a href="{{ $siteSettings['facebook'] ?? '#' }}"><i class="bi bi-facebook"></i></a>
                    <a href="{{ $siteSettings['instagram'] ?? '#' }}"><i class="bi bi-instagram"></i></a>
                    <a href="{{ $siteSettings['twitter'] ?? '#' }}"><i class="bi bi-twitter-x"></i></a>
                </div>
                <!-- Language switch -->
                <div class="dropdown">
                    <a class="dropdown-toggle small text-decoration-none" data-bs-toggle="dropdown" href="#"><i
                            class="bi bi-globe2"></i> {{ session('locale', 'en') === 'bn' ? 'বাংলা' : 'English' }}</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a></li>
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'bn') }}">বাংলা</a></li>
                    </ul>
                </div>
                <!-- Dark mode -->
                <button id="darkToggle" class="btn btn-sm btn-outline-light py-0 px-2"><i
                        class="bi bi-moon-stars"></i></button>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top main-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                @if (!empty($siteSettings['site_logo']))
                    <img src="{{ Storage::url($siteSettings['site_logo']) }}"
                        alt="{{ $siteSettings['site_name'] ?? config('app.name') }}" class="img-fluid" width="200">
                @else
                    <i class="bi bi-capsule-pill text-primary"></i>
                    {{ $siteSettings['site_name'] ?? config('app.name') }}
                @endif

            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainNav"><span
                    class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Categories</a>
                        <ul class="dropdown-menu">
                            @foreach ($navCategories ?? [] as $cat)
                                <li><a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}"
                            href="{{ route('shop.index') }}">Shop</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                            href="{{ route('about') }}">About</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                            href="{{ route('contact') }}">Contact</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('order.track') ? 'active' : '' }}"
                            href="{{ route('order.track') }}"><i class="bi bi-truck"></i> Track Order</a></li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3 fs-5"></i>
                            @php($cartCount = collect(session('cart', []))->sum())
                            @if ($cartCount > 0)
                                <span class="cart-badge">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item"><a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3">Admin
                            Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="text-white mb-3">
                        @if (!empty($siteSettings['site_logo']))
                            <img src="{{ Storage::url($siteSettings['site_logo']) }}"
                                alt="{{ $siteSettings['site_name'] ?? config('app.name') }}" class="img-fluid"
                                width="200">
                        @else
                            <i class="bi bi-capsule-pill text-primary"></i>
                            {{ $siteSettings['site_name'] ?? config('app.name') }}
                        @endif

                    </h5>
                    <p>{{ $siteSettings['site_tagline'] ?? 'Your Trusted Health Partner' }}</p>
                    <p class="small mb-1"><i class="bi bi-geo-alt"></i> {{ $siteSettings['address'] ?? '' }}</p>
                    <p class="small mb-1"><i class="bi bi-telephone"></i> {{ $siteSettings['phone'] ?? '' }}</p>
                    <p class="small mb-3"><i class="bi bi-envelope"></i> {{ $siteSettings['email'] ?? '' }}</p>
                    <div class="social-links">
                        <a href="{{ $siteSettings['facebook'] ?? '#' }}"><i class="bi bi-facebook"></i></a>
                        <a href="{{ $siteSettings['instagram'] ?? '#' }}"><i class="bi bi-instagram"></i></a>
                        <a href="{{ $siteSettings['twitter'] ?? '#' }}"><i class="bi bi-twitter-x"></i></a>
                        <a href="{{ $siteSettings['youtube'] ?? '#' }}"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('shop.index') }}">Shop</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-6">
                    <h6 class="text-white mb-3">Categories</h6>
                    <ul class="footer-links">
                        @foreach (($navCategories ?? [])->take(5) as $cat)
                            <li><a
                                    href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white mb-3">Newsletter</h6>
                    <p class="small">Subscribe for health tips &amp; special offers.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2">
                        @csrf

                        <input type="email" name="email" class="form-control form-control-sm"
                            placeholder="Your email" value="{{ old('email') }}" required>

                        <button type="submit" class="btn btn-primary btn-sm">
                            Join
                        </button>
                    </form>

                    @if (session('newsletter_success'))
                        <div class="alert alert-success alert-dismissible fade show mt-2 py-2 small" role="alert">

                            <i class="bi bi-check-circle me-1"></i>
                            {{ session('newsletter_success') }}

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @error('email')
                        <div class="text-danger small mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="footer-bottom py-3">
            <div
                class="container d-flex flex-column flex-md-row justify-content-between text-center text-md-start small">
                <span>{{ $siteSettings['footer_text'] ?? '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.' }}</span>
                <span>Developed with <i class="bi bi-heart-fill text-danger"></i> for better healthcare</span>
            </div>
        </div>
    </footer>

    <a href="#" id="backToTop" class="back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/venobox/2.2.0/venobox.min.js"
        integrity="sha512-ztCZGUO9gxeQieUeJxN9gDePafSh5Mj8W/u59I/akKlVB8CQjgvJekGUsKL78Zslq1xLKrMio/cvjnJGBG6F8Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/isotope/3.0.6/isotope.pkgd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/14.2.0/swiper-bundle.min.js"
        integrity="sha512-nM1ZmLe8KJ0bEkxcoG3D09bO8YvrITjvqMRPqEj14rYoNH/Hdg+ZR3hC+ez1S08A5NxEkRDgyBWVTZZgLQLhaQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.counterup/2.1.0/jquery.counterup.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
