<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProductService
{
    private const FIELDS = ['category_id', 'name', 'description', 'price', 'status'];

    /**
     * @param  array<string, mixed>  $data
     * @param  list<UploadedFile>  $images
     */
    public function create(array $data, array $images): Product
    {
        return $this->withFileCleanup(function (array &$stored) use ($data, $images) {
            return DB::transaction(function () use ($data, $images, &$stored) {
                $product = Product::create([
                    ...Arr::only($data, self::FIELDS),
                    'slug' => $this->uniqueSlug($data['name']),
                    'stock' => 0,
                ]);

                $this->syncVariants($product, $data['variants']);
                $this->attachImages($product, $images, $stored);

                return $product;
            });
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<UploadedFile>  $images
     */
    public function update(Product $product, array $data, array $images = []): Product
    {
        return $this->withFileCleanup(function (array &$stored) use ($product, $data, $images) {
            return DB::transaction(function () use ($product, $data, $images, &$stored) {
                $attributes = Arr::only($data, self::FIELDS);

                // Keep the URL stable unless the name actually changed.
                if ($product->name !== $data['name']) {
                    $attributes['slug'] = $this->uniqueSlug($data['name'], $product->id);
                }

                $product->update($attributes);
                $this->syncVariants($product, $data['variants']);
                $this->attachImages($product, $images, $stored);

                return $product;
            });
        });
    }

    /** @param  list<array{size: string, color: string, stock: int|string}>  $rows */
    private function syncVariants(Product $product, array $rows): void
    {
        $keep = [];

        foreach ($rows as $row) {
            $variant = $product->variants()->updateOrCreate(
                ['size' => $row['size'], 'color' => $row['color']],
                ['stock' => (int) $row['stock']],
            );
            $keep[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keep)->delete();

        // products.stock is a denormalised total; variants are the source of truth.
        $product->update(['stock' => (int) $product->variants()->sum('stock')]);
    }

    /** @param  list<UploadedFile>  $files */
    private function attachImages(Product $product, array $files, array &$stored): void
    {
        foreach ($files as $file) {
            $path = $file->store('products', 'public');
            $stored[] = $path;
            $product->images()->create(['image' => $path]);
        }
    }

    /** Remove files written to disk if the database transaction fails afterwards. */
    private function withFileCleanup(callable $callback): Product
    {
        $stored = [];

        try {
            return $callback($stored);
        } catch (Throwable $e) {
            Storage::disk('public')->delete($stored);

            throw $e;
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $suffix = 2;

        while (
            Product::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
