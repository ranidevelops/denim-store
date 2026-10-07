<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OrderService $orders,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', [
            'lines' => $lines,
            'totalMinor' => $this->cart->totalMinor($lines),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $quantities = $this->cart->quantities();

        if ($quantities === []) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        try {
            $order = $this->orders->place($request->user(), $request->validated(), $quantities);
        } catch (ValidationException $e) {
            // Stock or availability changed; send the customer back to fix the cart.
            return redirect()->route('cart.index')->withErrors($e->errors());
        }

        $this->cart->clear();

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('checkout.success', ['order' => $order->load('items')]);
    }
}
