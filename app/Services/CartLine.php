<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\Money;

final class CartLine
{
    public function __construct(
        public readonly ProductVariant $variant,
        public readonly Product $product,
        public readonly int $quantity,
    ) {
    }

    public function unitMinor(): int
    {
        return Money::toMinor($this->product->price);
    }

    public function subtotalMinor(): int
    {
        return $this->unitMinor() * $this->quantity;
    }

    /** True when stock dropped after the item was added to the cart. */
    public function exceedsStock(): bool
    {
        return $this->quantity > $this->variant->stock;
    }
}
