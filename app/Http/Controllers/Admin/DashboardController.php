<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
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
        ]);
    }
}
