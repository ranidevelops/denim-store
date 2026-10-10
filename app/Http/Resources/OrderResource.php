<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'total_amount' => (float) $this->total_amount,
            'customer_name' => $this->customer_name,
            'phone' => $this->phone,
            'shipping_address' => $this->shipping_address,
            'created_at' => $this->created_at->toIso8601String(),
            'items' => $this->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'size' => $item->size,
                'color' => $item->color,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'subtotal' => (float) $item->subtotal,
            ])->values()->all(),
        ];
    }
}
