<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'price', 'stock', 'status'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'status' => ProductStatus::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Active->value);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)->orWhere('description', 'like', $like);
        });
    }

    public function scopeInCategory(Builder $query, ?int $categoryId): Builder
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }

    public function scopeInCategorySlug(Builder $query, ?string $slug): Builder
    {
        return $slug
            ? $query->whereHas('category', fn (Builder $q) => $q->where('slug', $slug))
            : $query;
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        $enum = ProductStatus::tryFrom((string) $status);

        return $enum ? $query->where('status', $enum->value) : $query;
    }

    /** Size and color are matched on the same variant, and only variants with stock count. */
    public function scopeAvailableIn(Builder $query, ?string $size, ?string $color): Builder
    {
        if (! $size && ! $color) {
            return $query;
        }

        return $query->whereHas('variants', function (Builder $q) use ($size, $color) {
            $q->where('stock', '>', 0)
                ->when($size, fn (Builder $q) => $q->where('size', $size))
                ->when($color, fn (Builder $q) => $q->where('color', $color));
        });
    }

    public function scopePriceBetween(Builder $query, ?string $min, ?string $max): Builder
    {
        return $query
            ->when(is_numeric($min), fn (Builder $q) => $q->where('price', '>=', $min))
            ->when(is_numeric($max), fn (Builder $q) => $q->where('price', '<=', $max));
    }

    public function scopeSortedBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price')->orderBy('id'),
            'name' => $query->orderBy('name')->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /**
     * Apply storefront/API filters in one place.
     *
     * @param  array<string, mixed>  $filters  search, category, size, color, min_price, max_price, sort
     */
    public function scopeFilteredBy(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['search'] ?? null)
            ->inCategorySlug($filters['category'] ?? null)
            ->availableIn($filters['size'] ?? null, $filters['color'] ?? null)
            ->priceBetween($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->sortedBy($filters['sort'] ?? null);
    }

    public function isInStock(): bool
    {
        return $this->variants->contains(fn (ProductVariant $variant) => $variant->stock > 0);
    }

    /** @return Collection<int, string> */
    public function availableSizes(): Collection
    {
        return $this->availableOptions('size', config('denim.sizes'));
    }

    /** @return Collection<int, string> */
    public function availableColors(): Collection
    {
        return $this->availableOptions('color', config('denim.colors'));
    }

    /** Every size or color the product has a variant for, in configured order (stock ignored). */
    public function offeredOptions(string $attribute, array $order): Collection
    {
        return $this->variants->pluck($attribute)->unique()
            ->sortBy(fn ($value) => array_search($value, $order, true))->values();
    }

    private function availableOptions(string $attribute, array $order): Collection
    {
        return $this->variants->where('stock', '>', 0)->pluck($attribute)->unique()
            ->sortBy(fn ($value) => array_search($value, $order, true))->values();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
