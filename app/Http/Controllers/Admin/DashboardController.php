<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::where('status', OrderStatus::Pending->value)->count(),
            'deliveredOrders' => Order::where('status', OrderStatus::Delivered->value)->count(),
            'totalSales' => Order::sum('total_amount'),
            'recentOrders' => Order::with('user')->latest()->orderByDesc('id')->limit(5)->get(),
            'lowStock' => ProductVariant::with('product')
                ->whereHas('product', fn ($query) => $query->active())
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->orderBy('id')
                ->limit(8)
                ->get(),
        ]);
    }
}
