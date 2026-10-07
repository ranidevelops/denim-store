@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<h1 class="h3 mb-4">Orders</h1>

<form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search order number, customer, phone or email">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select" aria-label="Status">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-denim flex-grow-1" type="submit">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Order</th><th>Customer</th><th>Date</th><th class="text-center">Items</th><th class="text-end">Total</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="fw-semibold">{{ $order->order_number }}</td>
                        <td>
                            {{ $order->customer_name }}
                            <div class="small text-muted">{{ $order->user?->email }}</div>
                        </td>
                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td class="text-center">{{ $order->items_count }}</td>
                        <td class="text-end">{{ config('denim.currency') }}{{ number_format($order->total_amount, 2) }}</td>
                        <td><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                        <td class="text-end"><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $orders->links() }}</div>
@endsection
