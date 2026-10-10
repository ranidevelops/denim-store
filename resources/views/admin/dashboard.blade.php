@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="h3 mb-4">Dashboard</h1>

@php
    $cards = [
        ['Total Products', $totalProducts, 'primary'],
        ['Total Categories', $totalCategories, 'secondary'],
        ['Total Orders', $totalOrders, 'dark'],
        ['Pending Orders', $pendingOrders, 'warning'],
        ['Delivered Orders', $deliveredOrders, 'success'],
        ['Total Sales', config('denim.currency').number_format($totalSales, 2), 'info'],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach ($cards as [$label, $value, $color])
        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm border-0 border-start border-4 border-{{ $color }}">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">{{ $label }}</div>
                    <div class="fs-3 fw-semibold">{{ $value }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Recent orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="small">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a>
                                    <div class="small text-muted">{{ $order->customer_name }}</div>
                                </td>
                                <td class="text-end">{{ config('denim.currency') }}{{ number_format($order->total_amount, 2) }}</td>
                                <td class="text-end"><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-4">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white"><h2 class="h6 mb-0">Low stock (5 or fewer)</h2></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody>
                        @forelse ($lowStock as $variant)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.products.edit', $variant->product) }}" class="text-decoration-none">{{ $variant->product->name }}</a>
                                    <div class="small text-muted">Size {{ $variant->size }} &middot; {{ $variant->color }}</div>
                                </td>
                                <td class="text-end">
                                    @if ($variant->stock === 0)
                                        <span class="badge bg-danger">Out of stock</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $variant->stock }} left</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-4">Everything is well stocked.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
