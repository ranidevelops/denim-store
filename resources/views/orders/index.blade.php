@extends('layouts.app')

@section('title', 'My Orders | Indigo & Co.')

@section('content')
<h1 class="h3 mb-4">My orders</h1>

@if ($orders->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <p class="mb-3">You haven't placed any orders yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-denim">Start shopping</a>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Order</th><th>Date</th><th class="text-center">Items</th><th class="text-end">Total</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                            <td class="text-center">{{ $order->items_count }}</td>
                            <td class="text-end">{{ config('denim.currency') }}{{ number_format($order->total_amount, 2) }}</td>
                            <td><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                            <td class="text-end"><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endif
@endsection
