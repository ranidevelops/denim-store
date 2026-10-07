@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
    </ol>
</nav>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 mb-0">Order {{ $order->order_number }}</h1>
        <div class="text-muted small">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
    </div>
    <span class="badge fs-6 {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Size</th><th>Color</th><th class="text-end">Price</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->product_name }}</td>
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
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6">Update status</h2>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select mb-2 @error('status') is-invalid @enderror" aria-label="Order status">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback d-block mb-2">{{ $message }}</div> @enderror
                    <button type="submit" class="btn btn-denim w-100">Save status</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Customer</h2>
                <p class="mb-0">{{ $order->customer_name }}</p>
                <p class="mb-2 small text-muted">{{ $order->user?->email }}</p>
                <p class="mb-2">{{ $order->phone }}</p>
                <h2 class="h6 mt-3">Shipping address</h2>
                <p class="mb-0" style="white-space: pre-line;">{{ $order->shipping_address }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
