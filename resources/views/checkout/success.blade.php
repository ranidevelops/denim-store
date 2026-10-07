@extends('layouts.app')

@section('title', 'Order confirmed | Indigo & Co.')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h1 class="h3">Thank you, {{ $order->customer_name }}!</h1>
                    <p class="text-muted mb-1">Your order has been placed.</p>
                    <p class="mb-0">Order number: <strong>{{ $order->order_number }}</strong></p>
                    <span class="badge {{ $order->status->badgeClass() }} mt-2">{{ $order->status->label() }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr><th>Product</th><th>Size</th><th>Color</th><th class="text-end">Price</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->size }}</td>
                                    <td>{{ $item->color }}</td>
                                    <td class="text-end">{{ config('denim.currency') }}{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ config('denim.currency') }}{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th colspan="5" class="text-end">Total</th><th class="text-end">{{ config('denim.currency') }}{{ number_format($order->total_amount, 2) }}</th></tr>
                        </tfoot>
                    </table>
                </div>

                <h2 class="h6">Delivering to</h2>
                <p class="mb-1">{{ $order->customer_name }} &middot; {{ $order->phone }}</p>
                <p class="text-muted" style="white-space: pre-line;">{{ $order->shipping_address }}</p>

                <div class="text-center mt-4">
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-denim">View order details</a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Continue shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
