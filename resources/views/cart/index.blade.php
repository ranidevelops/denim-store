@extends('layouts.app')

@section('title', 'Your Cart | Indigo & Co.')

@section('content')
<h1 class="h3 mb-4">Your cart</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

@if ($lines->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <p class="mb-3">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="btn btn-denim">Continue shopping</a>
        </div>
    </div>
@else
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Product</th><th class="text-end">Price</th><th style="width:170px">Quantity</th><th class="text-end">Subtotal</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($lines as $line)
                                @php $image = $line->product->images->first(); @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if ($image)
                                                <img src="{{ $image->url }}" alt="{{ $line->product->name }}" width="56" height="56" class="rounded object-fit-cover">
                                            @else
                                                <div class="rounded bg-light" style="width:56px;height:56px"></div>
                                            @endif
                                            <div>
                                                <a href="{{ route('products.show', $line->product) }}" class="fw-semibold text-decoration-none">{{ $line->product->name }}</a>
                                                <div class="small text-muted">Size {{ $line->variant->size }} &middot; {{ $line->variant->color }}</div>
                                                @if ($line->exceedsStock())
                                                    <span class="badge bg-danger">Only {{ $line->variant->stock }} left</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ config('denim.currency') }}{{ number_format($line->unitMinor() / 100, 2) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('cart.update', $line->variant) }}" class="input-group input-group-sm">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $line->quantity }}" min="1" max="{{ max($line->variant->stock, 1) }}" class="form-control" aria-label="Quantity">
                                            <button type="submit" class="btn btn-outline-secondary">Update</button>
                                        </form>
                                    </td>
                                    <td class="text-end fw-semibold">{{ config('denim.currency') }}{{ number_format($line->subtotalMinor() / 100, 2) }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('cart.destroy', $line->variant) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Remove {{ $line->product->name }}">&times;</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Continue shopping</a>
                <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Remove everything from your cart?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Clear cart</button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Order summary</h2>
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>{{ config('denim.currency') }}{{ number_format($totalMinor / 100, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Shipping</span><span>Free</span></div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-semibold mb-3"><span>Grand total</span><span>{{ config('denim.currency') }}{{ number_format($totalMinor / 100, 2) }}</span></div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-denim w-100">Proceed to checkout</a>
                    @guest
                        <p class="small text-muted text-center mt-2 mb-0">You will be asked to log in first.</p>
                    @endguest
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
