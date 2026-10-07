<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'customer_name', 'phone',
        'shipping_address', 'total_amount', 'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'status' => OrderStatus::class,
        ];
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('order_number', 'like', $like)
                ->orWhere('customer_name', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhereHas('user', fn (Builder $user) => $user->where('email', 'like', $like));
        });
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        $enum = OrderStatus::tryFrom((string) $status);

        return $enum ? $query->where('status', $enum->value) : $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
