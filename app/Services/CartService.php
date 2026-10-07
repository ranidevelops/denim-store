<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Session cart. Only variant ids and quantities are stored; names, prices and
 * stock are always read from the database so the browser can never influence them.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    /** @return array<int, int> variant id => quantity */
    public function quantities(): array
    {
        return array_map('intval', session(self::SESSION_KEY, []));
    }

    public function count(): int
    {
        return array_sum($this->quantities());
    }

    /**
     * Lines for display. Items whose variant or product no longer exists (or was
     * deactivated) are dropped from the session here.
     *
     * @return Collection<int, CartLine>
     */
    public function lines(): Collection
    {
        $quantities = $this->quantities();

        if ($quantities === []) {
            return collect();
        }

        $variants = ProductVariant::with('product.images')
            ->whereIn('id', array_keys($quantities))
            ->get()
            ->keyBy('id');

        $lines = collect();
        $valid = [];

        foreach ($quantities as $variantId => $quantity) {
            $variant = $variants->get($variantId);
            $product = $variant?->product;

            if (! $product || $product->status !== ProductStatus::Active) {
                continue;
            }

            $lines->push(new CartLine($variant, $product, $quantity));
            $valid[$variantId] = $quantity;
        }

        if (count($valid) !== count($quantities)) {
            session([self::SESSION_KEY => $valid]);
        }

        return $lines;
    }

    /** @param  Collection<int, CartLine>  $lines */
    public function totalMinor(Collection $lines): int
    {
        return $lines->sum(fn (CartLine $line) => $line->subtotalMinor());
    }

    public function add(Product $product, string $size, string $color, int $quantity): void
    {
        if ($product->status !== ProductStatus::Active) {
            throw ValidationException::withMessages(['product' => 'This product is no longer available.']);
        }

        $variant = $product->variants()->where('size', $size)->where('color', $color)->first();

        if (! $variant) {
            throw ValidationException::withMessages(['size' => 'That size and color combination is not available.']);
        }

        if ($variant->stock < 1) {
            throw ValidationException::withMessages(['size' => 'That size and color is out of stock.']);
        }

        // Same product + size + color is one cart line, so quantities merge.
        $inCart = $this->quantities()[$variant->id] ?? 0;
        $this->assertWithinStock($variant, $inCart + $quantity, $inCart);

        $this->store($variant->id, $inCart + $quantity);
    }

    public function update(ProductVariant $variant, int $quantity): void
    {
        abort_unless(isset($this->quantities()[$variant->id]), 404);

        $this->assertWithinStock($variant, $quantity);

        $this->store($variant->id, $quantity);
    }

    public function remove(ProductVariant $variant): void
    {
        $quantities = $this->quantities();
        unset($quantities[$variant->id]);

        session([self::SESSION_KEY => $quantities]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    private function assertWithinStock(ProductVariant $variant, int $requested, int $alreadyInCart = 0): void
    {
        if ($requested <= $variant->stock) {
            return;
        }

        $message = "Only {$variant->stock} available for that size and color";
        $message .= $alreadyInCart > 0 ? " (you already have {$alreadyInCart} in your cart)." : '.';

        throw ValidationException::withMessages(['quantity' => $message]);
    }

    private function store(int $variantId, int $quantity): void
    {
        $quantities = $this->quantities();
        $quantities[$variantId] = $quantity;

        session([self::SESSION_KEY => $quantities]);
    }
}
