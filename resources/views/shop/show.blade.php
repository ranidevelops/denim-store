@extends('layouts.app')

@section('title', $product->name.' | Indigo & Co.')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        @if ($product->category)
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
        @endif
        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-md-6">
        @php $firstImage = $product->images->first(); @endphp
        <div class="ratio ratio-1x1 bg-white border rounded overflow-hidden mb-3">
            @if ($firstImage)
                <img id="main-image" src="{{ $firstImage->url }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
            @else
                <span class="d-flex align-items-center justify-content-center text-muted">No image</span>
            @endif
        </div>
        @if ($product->images->count() > 1)
            <div class="d-flex flex-wrap gap-2">
                @foreach ($product->images as $image)
                    <img src="{{ $image->url }}" alt="{{ $product->name }} view {{ $loop->iteration }}"
                         class="gallery-thumb rounded border {{ $loop->first ? 'active' : '' }}" data-full="{{ $image->url }}">
                @endforeach
            </div>
        @endif
    </div>

    <div class="col-md-6">
        <div class="text-muted small">{{ $product->category?->name }}</div>
        <h1 class="h3">{{ $product->name }}</h1>
        <div class="fs-4 fw-semibold mb-2">{{ config('denim.currency') }}{{ number_format($product->price, 2) }}</div>

        @if ($product->isInStock())
            <span class="badge bg-success mb-3">In stock</span>
        @else
            <span class="badge bg-danger mb-3">Out of stock</span>
        @endif

        @if ($product->description)
            <p class="text-body-secondary" style="white-space: pre-line;">{{ $product->description }}</p>
        @endif

        <hr>
        <form method="POST" action="{{ route('cart.store', $product) }}" novalidate>
            @csrf
            @include('shop._purchase-panel')
        </form>
    </div>
</div>
@if ($related->isNotEmpty())
    <section class="mt-5">
        <h2 class="h5 mb-3">You may also like</h2>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach ($related as $item)
                <div class="col">
                    <x-product-card :product="$item" />
                </div>
            @endforeach
        </div>
    </section>
@endif
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.gallery-thumb').forEach((thumb) => {
        thumb.addEventListener('click', () => {
            document.getElementById('main-image').src = thumb.dataset.full;
            document.querySelectorAll('.gallery-thumb').forEach((t) => t.classList.remove('active'));
            thumb.classList.add('active');
        });
    });
</script>
@endpush
