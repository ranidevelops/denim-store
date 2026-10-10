@extends('layouts.app')

@section('title', 'Your Cart | Indigo & Co.')

@section('content')
<h1 class="h3 mb-4">Your cart</h1>

<div id="cart-alert"></div>

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
                                <tr data-variant="{{ $line->variant->id }}">
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
                                        <form method="POST" action="{{ route('cart.update', $line->variant) }}" class="input-group input-group-sm js-cart-form">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $line->quantity }}" min="1" max="{{ max($line->variant->stock, 1) }}" class="form-control" aria-label="Quantity">
                                            <button type="submit" class="btn btn-outline-secondary">Update</button>
                                        </form>
                                    </td>
                                    <td class="text-end fw-semibold" data-subtotal>{{ config('denim.currency') }}{{ number_format($line->subtotalMinor() / 100, 2) }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('cart.destroy', $line->variant) }}" class="js-cart-form">
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
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span data-cart-total>{{ config('denim.currency') }}{{ number_format($totalMinor / 100, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Shipping</span><span>Free</span></div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-semibold mb-3"><span>Grand total</span><span data-cart-total>{{ config('denim.currency') }}{{ number_format($totalMinor / 100, 2) }}</span></div>
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

@push('scripts')
<script>
    // Progressive enhancement: if fetch fails for any reason the form is submitted normally.
    (() => {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const alertBox = document.getElementById('cart-alert');
        const badge = document.getElementById('cart-badge');

        function showAlert(type, message) {
            alertBox.innerHTML = '';
            const box = document.createElement('div');
            box.className = `alert alert-${type}`;
            box.textContent = message;
            alertBox.appendChild(box);
        }

        function applyCart(cart, message) {
            if (badge) {
                badge.textContent = cart.count;
                badge.classList.toggle('d-none', cart.count < 1);
            }
            if (cart.count === 0) {
                window.location.reload();
                return;
            }

            document.querySelectorAll('[data-cart-total]').forEach((el) => { el.textContent = cart.total; });

            document.querySelectorAll('tr[data-variant]').forEach((row) => {
                const line = cart.lines.find((l) => String(l.variant_id) === row.dataset.variant);
                if (!line) {
                    row.remove();
                    return;
                }
                row.querySelector('[data-subtotal]').textContent = line.subtotal;
                row.querySelector('input[name="quantity"]').value = line.quantity;
            });

            showAlert('success', message);
        }

        document.querySelectorAll('.js-cart-form').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const button = form.querySelector('button');
                button.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const data = await response.json();

                    if (response.ok) {
                        applyCart(data.cart, data.message);
                    } else if (response.status === 422) {
                        showAlert('danger', Object.values(data.errors || {}).flat().join(' ') || data.message);
                    } else {
                        form.submit();
                    }
                } catch (error) {
                    form.submit();
                } finally {
                    button.disabled = false;
                }
            });
        });
    })();
</script>
@endpush
