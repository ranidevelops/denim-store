<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ $showHero ? 'New arrivals' : 'Products' }}</h1>
    <span class="text-muted small">{{ $products->total() }} {{ Str::plural('item', $products->total()) }}</span>
</div>

@if ($products->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <p class="mb-3">No products match your search.</p>
            <a href="{{ route('products.index') }}" class="btn btn-denim btn-sm js-clear-filters">Clear filters</a>
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
