@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-denim">Add product</a>
</div>

<form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or description">
    </div>
    <div class="col-md-3">
        <select name="category" class="form-select" aria-label="Category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select" aria-label="Status">
            <option value="">Any status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-denim flex-grow-1" type="submit">Filter</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            @if ($image = $product->images->first())
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="thumb">
                            @else
                                <div class="thumb-empty"></div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $product->name }}</td>
                        <td>{{ $product->category?->name }}</td>
                        <td>{{ config('denim.currency') }}{{ number_format($product->price, 2) }}</td>
                        <td>
                            @if ($product->stock > 0) {{ $product->stock }} @else <span class="badge bg-danger">Out of stock</span> @endif
                        </td>
                        <td>
                            <span class="badge {{ $product->status->value === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $product->status->label() }}</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $products->links() }}</div>
@endsection
