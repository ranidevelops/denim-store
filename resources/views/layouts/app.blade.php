<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Indigo & Co. Denim')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --denim: #1f3a5f; --denim-dark: #14263f; --stitch: #c9a24d; }
        body { background: #f7f8fa; }
        .navbar-denim { background: var(--denim-dark); }
        .brand { font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .brand span { color: var(--stitch); }
        .btn-denim { background: var(--denim); border-color: var(--denim); color: #fff; }
        .btn-denim:hover { background: var(--denim-dark); border-color: var(--denim-dark); color: #fff; }
        .hero { background: linear-gradient(135deg, var(--denim-dark), var(--denim)); color: #fff; }
        .product-card { transition: transform .15s ease, box-shadow .15s ease; }
        .product-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important; }
        .product-card .card-title a { color: inherit; text-decoration: none; }
        .gallery-thumb { width: 72px; height: 72px; object-fit: cover; cursor: pointer; }
        .gallery-thumb.active { outline: 2px solid var(--denim); }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-denim">
        <div class="container">
            <a class="navbar-brand brand" href="{{ route('home') }}">Indigo <span>&amp;</span> Co.</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-lg-3">
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">All Products</a></li>
                    @if ($navCategories->isNotEmpty())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
                            <ul class="dropdown-menu">
                                @foreach ($navCategories as $navCategory)
                                    <li><a class="dropdown-item" href="{{ route('products.index', ['category' => $navCategory->slug]) }}">{{ $navCategory->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>

                <form class="d-flex flex-grow-1 my-2 my-lg-0 me-lg-3" role="search" method="GET" action="{{ route('products.index') }}">
                    <input class="form-control form-control-sm" type="search" name="search" value="{{ request('search') }}"
                           placeholder="Search denim..." maxlength="100" aria-label="Search products">
                    <button class="btn btn-sm btn-outline-light ms-2" type="submit">Search</button>
                </form>

                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            Cart
                            @if ($cartCount > 0)
                                <span class="badge rounded-pill bg-warning text-dark">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                    @auth
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">My Orders</a></li>
                        <li class="nav-item"><span class="navbar-text">Hi, {{ auth()->user()->name }}</span></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @include('partials.alerts')
        @yield('content')
    </main>

    <footer class="border-top py-3 text-center text-muted small">
        &copy; {{ date('Y') }} Indigo &amp; Co. Denim
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
