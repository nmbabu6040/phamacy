<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }} Admin</title>
    <link rel="icon" type="image/x-icon"
        href="{{ !empty($siteSettings['site_favicon']) ? Storage::url($siteSettings['site_favicon']) : asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    @stack('styles')
</head>

<body>

    <div id="preloader">
        <div class="spinner"></div>
    </div>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <i class="bi bi-capsule-pill"></i> <span>{{ config('app.name') }}</span>
            </div>
            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

                <li class="nav-heading">Inventory</li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                        href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam"></i> Products /
                        Medicine</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                        href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Categories</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.generics.*') ? 'active' : '' }}"
                        href="{{ route('admin.generics.index') }}"><i class="bi bi-capsule"></i> Generic Names</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                        href="{{ route('admin.brands.index') }}"><i class="bi bi-award"></i> Brands</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}"
                        href="{{ route('admin.units.index') }}"><i class="bi bi-rulers"></i> Units</a></li>


                <li class="nav-heading">Purchase & Sales</li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.sales.pos') ? 'active' : '' }}"
                        href="{{ route('admin.sales.pos') }}"><i class="bi bi-cash-coin"></i> POS (New Sale)</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.sales.index') ? 'active' : '' }}"
                        href="{{ route('admin.sales.index') }}"><i class="bi bi-receipt"></i> Sales</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                        href="{{ route('admin.orders.index') }}"><i class="bi bi-bag-check"></i> Online Orders</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}"
                        href="{{ route('admin.purchases.index') }}"><i class="bi bi-truck"></i> Purchases</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}"
                        href="{{ route('admin.suppliers.index') }}"><i class="bi bi-building"></i> Suppliers</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                        href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i> Customers</a></li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.batches.index') ? 'active' : '' }}"
                        href="{{ route('admin.batches.index') }}"><i class="bi bi-boxes"></i> Batches</a></li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.batches.expiring') ? 'active' : '' }}"
                        href="{{ route('admin.batches.expiring') }}"><i class="bi bi-hourglass-split"></i> Expiry
                        Alerts</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.barcodes.*') ? 'active' : '' }}"
                        href="{{ route('admin.barcodes.index') }}"><i class="bi bi-upc-scan"></i> Barcode / QR
                        Labels</a></li>

                <li class="nav-heading">Finance</li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}"
                        href="{{ route('admin.expenses.index') }}"><i class="bi bi-wallet2"></i> Expenses</a></li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.expense-categories.*') ? 'active' : '' }}"
                        href="{{ route('admin.expense-categories.index') }}"><i class="bi bi-folder2"></i> Expense
                        Categories</a></li>

                <li class="nav-heading">Reports</li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}"
                        href="{{ route('admin.reports.sales') }}"><i class="bi bi-graph-up"></i> Sales Report</a></li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.reports.purchases') ? 'active' : '' }}"
                        href="{{ route('admin.reports.purchases') }}"><i class="bi bi-graph-down"></i> Purchase
                        Report</a></li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.reports.expenses') ? 'active' : '' }}"
                        href="{{ route('admin.reports.expenses') }}"><i class="bi bi-cash-stack"></i> Expense
                        Report</a></li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.reports.profit-loss') ? 'active' : '' }}"
                        href="{{ route('admin.reports.profit-loss') }}"><i class="bi bi-bar-chart-line"></i> Profit
                        &amp; Loss</a></li>
                <li class="nav-item"><a
                        class="nav-link {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}"
                        href="{{ route('admin.reports.inventory') }}"><i class="bi bi-clipboard-data"></i> Inventory
                        Report</a></li>

                @if (auth()->user()?->isAdmin())
                    <li class="nav-heading">Administration</li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}"><i class="bi bi-person-badge"></i> Staff /
                            Users</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                            href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock"></i> Roles &
                            Permissions</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}"
                            href="{{ route('admin.branches.index') }}"><i class="bi bi-diagram-3"></i> Branches</a>
                    </li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}"
                            href="{{ route('admin.sliders.index') }}"><i class="bi bi-images"></i> Homepage
                            Sliders</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.counters.*') ? 'active' : '' }}"
                            href="{{ route('admin.counters.index') }}"><i class="bi bi-123"></i> Counters</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"
                            href="{{ route('admin.faqs.index') }}"><i class="bi bi-question-circle"></i> FAQ</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.about-features.*') ? 'active' : '' }}"
                            href="{{ route('admin.about-features.index') }}"><i class="bi bi-list-check"></i> About
                            Features</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.about-values.*') ? 'active' : '' }}"
                            href="{{ route('admin.about-values.index') }}"><i class="bi bi-award"></i> About
                            Values</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                            href="{{ route('admin.settings.index') }}"><i class="bi bi-gear"></i> Site Settings &
                            SEO</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}"
                            href="{{ route('admin.activity-logs.index') }}"><i class="bi bi-clock-history"></i>
                            Activity Log</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}"
                            href="{{ route('admin.backups.index') }}"><i class="bi bi-cloud-arrow-down"></i>
                            Backup</a></li>
                @endif
            </ul>
        </aside>

        <!-- Main -->
        <div class="main-content">
            <nav class="topbar">
                <button id="sidebarToggle" class="btn btn-sm btn-light"><i class="bi bi-list"></i></button>
                <div class="ms-auto d-flex align-items-center gap-3">
                    @if (($allBranches ?? collect())->count())
                        <div class="dropdown">
                            <a class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                                href="#">
                                <i class="bi bi-shop"></i> {{ $currentBranch->name ?? 'Select Branch' }}
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($allBranches as $b)
                                    <li>
                                        <form action="{{ route('admin.branches.switch') }}" method="POST">@csrf
                                            <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                            <button type="submit"
                                                class="dropdown-item {{ $currentBranch?->id === $b->id ? 'active' : '' }}">{{ $b->name }}
                                                @if ($b->is_main)
                                                    <span class="badge bg-primary ms-1">Main</span>
                                                @endif
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif($currentBranch ?? null)
                        <span class="badge bg-light text-dark border"><i class="bi bi-shop"></i>
                            {{ $currentBranch->name }}</span>
                    @endif
                    <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary"><i
                            class="bi bi-moon-stars"></i></button>
                    <div class="dropdown">
                        <a class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
                            data-bs-toggle="dropdown" href="#">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'U') }}"
                                class="rounded-circle" width="32" height="32">
                            <span>{{ auth()->user()->name ?? 'Guest' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span
                                    class="dropdown-item-text text-muted small">{{ auth()->user()->role->name ?? '' }}</span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">@csrf
                                    <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i>
                                        Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="page-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i>
                        {{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show"><i
                            class="bi bi-exclamation-triangle"></i> {{ session('error') }}<button class="btn-close"
                            data-bs-dismiss="alert"></button></div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/admin.js') }}"></script>
    @stack('scripts')
</body>

</html>
