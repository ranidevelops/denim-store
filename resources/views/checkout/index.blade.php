@extends('layouts.app')

@section('title', 'Checkout | Indigo & Co.')

@section('content')
<h1 class="h3 mb-4">Checkout</h1>

<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('checkout.store') }}" class="card border-0 shadow-sm" novalidate>
            @csrf
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Delivery details</h2>

                <div class="mb-3">
                    <label for="customer_name" class="form-label">Full name</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}"
                           class="form-control @error('customer_name') is-invalid @enderror" maxlength="255" required>
                    @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                           class="form-control @error('phone') is-invalid @enderror" maxlength="20" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="shipping_address" class="form-label">Shipping address</label>
                    <textarea id="shipping_address" name="shipping_address" rows="4" maxlength="1000"
                              class="form-control @error('shipping_address') is-invalid @enderror" required>{{ old('shipping_address') }}</textarea>
                    @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="alert alert-info small">Payment is simulated: no card is charged. Your order is placed as pay on delivery.</div>

                <button type="submit" class="btn btn-denim btn-lg w-100">Place order</button>
            </div>
        </form>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3">Order summary</h2>
                <ul class="list-unstyled mb-3">
                    @foreach ($lines as $line)
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $line->product->name }}</div>
                                <div class="small text-muted">Size {{ $line->variant->size }} &middot; {{ $line->variant->color }} &middot; Qty {{ $line->quantity }}</div>
                            </div>
                            <div>{{ config('denim.currency') }}{{ number_format($line->subtotalMinor() / 100, 2) }}</div>
                        </li>
                    @endforeach
                </ul>
                <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span>Free</span></div>
                <div class="d-flex justify-content-between fs-5 fw-semibold"><span>Total</span><span>{{ config('denim.currency') }}{{ number_format($totalMinor / 100, 2) }}</span></div>
                <a href="{{ route('cart.index') }}" class="small d-inline-block mt-3">Edit cart</a>
            </div>
        </div>
    </div>
</div>
@endsection
