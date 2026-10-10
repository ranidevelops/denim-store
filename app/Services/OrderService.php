<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order from cart quantities. Prices and stock come only from the
     * database, read inside a transaction while the variant rows are locked.
     *
     * @param  array{customer_name: string, phone: string, shipping_address: string}  $details
     * @param  array<int, int>  $quantities  variant id => quantity
     */
    public function place(User $user, array $details, array $quantities): Order
    {
        if ($quantities === []) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        return DB::transaction(function () use ($user, $details, $quantities) {
            ksort($quantities); // consistent lock order prevents deadlocks between concurrent orders

            // lockForUpdate makes concurrent checkouts for the same variant wait for each other.
            $variants = ProductVariant::whereIn('id', array_keys($quantities))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $products = Product::whereIn('id', $variants->pluck('product_id')->unique())
                ->get()
                ->keyBy('id');

            $items = [];
            $totalMinor = 0;

            foreach ($quantities as $variantId => $quantity) {
                $variant = $variants->get($variantId);
                $product = $variant ? $products->get($variant->product_id) : null;

                if (! $product || $product->status !== ProductStatus::Active) {
                    throw ValidationException::withMessages([
                        'cart' => 'An item in your cart is no longer available. Please review your cart.',
                    ]);
                }

                if ($quantity < 1 || $variant->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Not enough stock for {$product->name} ({$variant->size}, {$variant->color}). Only {$variant->stock} left.",
                    ]);
                }

                $variant->decrement('stock', $quantity);

                $unitMinor = Money::toMinor($product->price);
                $totalMinor += $unitMinor * $quantity;

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'quantity' => $quantity,
                    'price' => Money::decimal($unitMinor),
                    'subtotal' => Money::decimal($unitMinor * $quantity),
                ];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $details['customer_name'],
                'phone' => $details['phone'],
                'shipping_address' => $details['shipping_address'],
                'total_amount' => Money::decimal($totalMinor),
                'status' => OrderStatus::Pending,
            ]);

            $order->items()->createMany($items);

            foreach ($products->keys() as $productId) {
                Product::whereKey($productId)->update([
                    'stock' => (int) ProductVariant::where('product_id', $productId)->sum('stock'),
                ]);
            }

            return $order;
        });
    }

    /**
     * Turn API order lines (product + size + color + quantity) into variant id => quantity.
     * Duplicate lines are merged. Stock itself is checked later, inside place().
     *
     * @param  list<array{product_id: int, size: string, color: string, quantity: int}>  $items
     * @return array<int, int>
     */
    public function variantQuantities(array $items): array
    {
        $quantities = [];

        foreach ($items as $index => $item) {
            $variant = ProductVariant::query()
                ->where('product_id', $item['product_id'])
                ->where('size', $item['size'])
                ->where('color', $item['color'])
                ->whereHas('product', fn ($query) => $query->active())
                ->first();

            if (! $variant) {
                throw ValidationException::withMessages([
                    "items.{$index}.size" => 'That size and color is not available for this product.',
                ]);
            }

            $quantities[$variant->id] = ($quantities[$variant->id] ?? 0) + (int) $item['quantity'];
        }

        return $quantities;
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'DN'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
