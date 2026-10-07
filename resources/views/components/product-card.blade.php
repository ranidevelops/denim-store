@props(['product'])

@php
    $image = $product->images->first();
    $inStock = $product->isInStock();
@endphp

<div class="card h-100 shadow-sm border-0 product-card">
    <a href="{{ route('products.show', $product) }}" class="ratio ratio-1x1 bg-light rounded-top overflow-hidden" aria-label="{{ $product->name }}">
        @if ($image)
            <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover" loading="lazy">
        @else
            <span class="d-flex align-items-center justify-content-center text-muted small">No image</span>
        @endif
    </a>
    <div class="card-body d-flex flex-column">
        <div class="text-muted small">{{ $product->category?->name }}</div>
        <h2 class="h6 card-title mb-1"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h2>
        <div class="fw-semibold mb-2">{{ config('denim.currency') }}{{ number_format($product->price, 2) }}</div>

        @if ($inStock)
            <div class="small text-muted">Sizes: {{ $product->availableSizes()->implode(', ') }}</div>
            <div class="small text-muted mb-2">Colors: {{ $product->availableColors()->implode(', ') }}</div>
            <span class="badge bg-success align-self-start mb-3">In stock</span>
        @else
            <span class="badge bg-danger align-self-start mb-3 mt-auto">Out of stock</span>
        @endif

        <a href="{{ route('products.show', $product) }}" class="btn btn-denim btn-sm mt-auto">View Details</a>
    </div>
</div>
