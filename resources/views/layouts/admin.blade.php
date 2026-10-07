<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | Indigo &amp; Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --denim: #1f3a5f; --denim-dark: #14263f; --stitch: #c9a24d; }
        body { background: #f1f3f6; }
        .sidebar { background: var(--denim-dark); }
        .sidebar .nav-link { color: #cdd6e3; border-radius: .375rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: var(--denim); color: #fff; }
        .brand { font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #fff; text-decoration: none; }
        .brand span { color: var(--stitch); }
        .btn-denim { background: var(--denim); border-color: var(--denim); color: #fff; }
        .btn-denim:hover { background: var(--denim-dark); border-color: var(--denim-dark); color: #fff; }
        .thumb { width: 48px; height: 48px; object-fit: cover; border-radius: .375rem; }
        .thumb-empty { width: 48px; height: 48px; border-radius: .375rem; background: #e3e6eb; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column">
                <a href="{{ route('admin.dashboard') }}" class="brand fs-5 mb-4 d-block">Indigo <span>&amp;</span> Admin</a>
                <ul class="nav nav-pills flex-column gap-1 mb-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Orders</a>
                    </li>
                </ul>
                <hr class="border-secondary">
                <a class="nav-link text-light small px-0" href="{{ route('home') }}">View storefront</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="btn btn-outline-light btn-sm w-100" type="submit">Logout</button>
                </form>
            </aside>

            <main class="col-md-9 col-lg-10 p-4">
                @include('partials.alerts')
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
