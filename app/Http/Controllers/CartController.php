<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartLine;
use App\Services\CartService;
use App\Support\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function update(UpdateCartRequest $request, ProductVariant $variant): RedirectResponse|JsonResponse
    {
        $this->cart->update($variant, $request->validated('quantity'));

        return $this->respond($request, 'Cart updated.');
    }

    public function destroy(Request $request, ProductVariant $variant): RedirectResponse|JsonResponse
    {
        $this->cart->remove($variant);

        return $this->respond($request, 'Item removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()->route('cart.index')->with('success', 'Your cart has been cleared.');
    }

    /** Same action serves normal form posts (redirect) and fetch() calls (JSON). */
    private function respond(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if (! $request->expectsJson()) {
            return redirect()->route('cart.index')->with('success', $message);
        }

        $lines = $this->cart->lines();
        $currency = config('denim.currency');

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart' => [
                'count' => $this->cart->count(),
                'total' => $currency.Money::format($this->cart->totalMinor($lines)),
                'lines' => $lines->map(fn (CartLine $line) => [
                    'variant_id' => $line->variant->id,
                    'quantity' => $line->quantity,
                    'subtotal' => $currency.Money::format($line->subtotalMinor()),
                ])->values(),
            ],
        ]);
    }
}
