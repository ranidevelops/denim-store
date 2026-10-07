@extends('layouts.app')

@section('title', 'Shop Denim | Indigo & Co.')

@section('content')
@if ($showHero)
    <section class="hero rounded-3 p-5 text-center mb-4">
        <h1 class="display-5 fw-bold">Denim, made to last</h1>
        <p class="lead mb-0">Jeans, jackets and everyday essentials from Indigo &amp; Co.</p>
    </section>
@endif

@if ($errors->any())
    <div class="alert alert-warning">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <aside class="col-lg-3">
        <button class="btn btn-outline-secondary w-100 d-lg-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#filters" aria-expanded="false" aria-controls="filters">
            Filters
        </button>

        <div class="collapse d-lg-block" id="filters">
            <form method="GET" action="{{ route('products.index') }}" class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 mb-3">Filter products</h2>

                    <div class="mb-3">
                        <label for="f-search" class="form-label small mb-1">Search</label>
                        <input type="search" id="f-search" name="search" value="{{ $filters->get('search') }}" maxlength="100" class="form-control form-control-sm" placeholder="Name or description">
                    </div>

                    <div class="mb-3">
                        <label for="f-category" class="form-label small mb-1">Category</label>
                        <select id="f-category" name="category" class="form-select form-select-sm">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected($filters->get('category') === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="f-size" class="form-label small mb-1">Size</label>
                            <select id="f-size" name="size" class="form-select form-select-sm">
                                <option value="">Any</option>
                                @foreach ($sizes as $size)
                                    <option value="{{ $size }}" @selected($filters->get('size') === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="f-color" class="form-label small mb-1">Color</label>
                            <select id="f-color" name="color" class="form-select form-select-sm">
                                <option value="">Any</option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color }}" @selected($filters->get('color') === $color)>{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small mb-1">Price ({{ config('denim.currency') }})</label>
                        <div class="input-group input-group-sm">
                            <input type="number" min="0" step="1" name="min_price" value="{{ $filters->get('min_price') }}" class="form-control" placeholder="Min" aria-label="Minimum price">
                            <input type="number" min="0" step="1" name="max_price" value="{{ $filters->get('max_price') }}" class="form-control" placeholder="Max" aria-label="Maximum price">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="f-sort" class="form-label small mb-1">Sort by</label>
                        <select id="f-sort" name="sort" class="form-select form-select-sm">
                            @foreach ($sorts as $value => $label)
                                <option value="{{ $value }}" @selected(($filters->get('sort') ?? 'newest') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-denim btn-sm">Apply filters</button>
                        @if ($hasFilters)
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Clear filters</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </aside>

    <section class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">{{ $showHero ? 'New arrivals' : 'Products' }}</h1>
            <span class="text-muted small">{{ $products->total() }} {{ Str::plural('item', $products->total()) }}</span>
        </div>

        @if ($products->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <p class="mb-3">No products match your search.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-denim btn-sm">Clear filters</a>
                </div>
            </div>
        @else
            <div class="row row-cols-2 row-cols-md-3 g-3">
                @foreach ($products as $product)
                    <div class="col">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        @endif
    </section>
</div>
@endsection
