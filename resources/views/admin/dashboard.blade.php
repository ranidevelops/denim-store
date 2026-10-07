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

<div class="row g-3">
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
@endsection
