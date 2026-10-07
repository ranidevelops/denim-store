<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        // Querying through the relationship means only the user's own orders can ever be listed.
        $orders = $request->user()->orders()
            ->withCount('items')
            ->latest()
            ->orderByDesc('id')
            ->paginate(10);

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('orders.show', ['order' => $order->load('items')]);
    }
}
