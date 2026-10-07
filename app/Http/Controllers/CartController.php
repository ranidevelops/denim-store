<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(): View
    {
        $lines = $this->cart->lines();

        return view('cart.index', [
            'lines' => $lines,
            'totalMinor' => $this->cart->totalMinor($lines),
        ]);
    }

    public function store(AddToCartRequest $request, Product $product): RedirectResponse
    {
        $this->cart->add($product, $request->validated('size'), $request->validated('color'), $request->validated('quantity'));

        return redirect()->route('cart.index')->with('success', $product->name.' was added to your cart.');
    }

    public function update(UpdateCartRequest $request, ProductVariant $variant): RedirectResponse
    {
        $this->cart->update($variant, $request->validated('quantity'));

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(ProductVariant $variant): RedirectResponse
    {
        $this->cart->remove($variant);

        return redirect()->route('cart.index')->with('success', 'Item removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()->route('cart.index')->with('success', 'Your cart has been cleared.');
    }
}
