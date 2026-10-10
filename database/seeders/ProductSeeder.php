<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private const COLOR_HEX = [
        'Black' => '#25272b',
        'Blue' => '#3b6ea5',
        'Dark Blue' => '#1f3a5f',
        'Light Blue' => '#7fa7d1',
        'Grey' => '#7b818c',
        'White' => '#b9bec7',
    ];

    /** @var list<array{name: string, category: string, price: int, description: string, sizes: list<string>, colors: list<string>}> */
    private const PRODUCTS = [
        ['name' => 'Classic Straight Fit Jeans', 'category' => 'Jeans', 'price' => 1999, 'sizes' => ['S', 'M', 'L', 'XL'], 'colors' => ['Blue', 'Dark Blue'],
            'description' => 'Mid-rise straight leg jeans in durable 12 oz cotton denim. A wardrobe staple that gets better with every wash.'],
        ['name' => 'Slim Fit Stretch Jeans', 'category' => 'Jeans', 'price' => 2299, 'sizes' => ['S', 'M', 'L', 'XL'], 'colors' => ['Dark Blue', 'Black'],
            'description' => 'Slim through the leg with two-way stretch, so it moves with you from desk to weekend.'],
        ['name' => 'Relaxed Fit Light Wash Jeans', 'category' => 'Jeans', 'price' => 2499, 'sizes' => ['M', 'L', 'XL', 'XXL'], 'colors' => ['Light Blue', 'Blue'],
            'description' => 'Roomy through the seat and thigh with a softly faded wash. Easy, comfortable and made for long days.'],
        ['name' => 'Black Skinny Jeans', 'category' => 'Jeans', 'price' => 1899, 'sizes' => ['XS', 'S', 'M', 'L'], 'colors' => ['Black', 'Grey'],
            'description' => 'High-stretch skinny jeans with a clean, sharp finish. Holds its colour wash after wash.'],
        ['name' => 'Classic Trucker Jacket', 'category' => 'Jackets', 'price' => 3499, 'sizes' => ['M', 'L', 'XL'], 'colors' => ['Blue', 'Dark Blue'],
            'description' => 'The original denim jacket with a button front, chest pockets and a structured fit that layers over anything.'],
        ['name' => 'Sherpa Lined Denim Jacket', 'category' => 'Jackets', 'price' => 4299, 'sizes' => ['M', 'L', 'XL', 'XXL'], 'colors' => ['Dark Blue', 'Black'],
            'description' => 'Rugged denim outside, soft sherpa lining inside. Built for cold mornings.'],
        ['name' => 'Western Denim Shirt', 'category' => 'Shirts', 'price' => 1799, 'sizes' => ['S', 'M', 'L', 'XL'], 'colors' => ['Blue', 'Light Blue'],
            'description' => 'Snap-button western shirt with a double yoke and pointed pockets, cut from lightweight denim.'],
        ['name' => 'Chambray Button-Down Shirt', 'category' => 'Shirts', 'price' => 1599, 'sizes' => ['S', 'M', 'L', 'XL'], 'colors' => ['Light Blue', 'White'],
            'description' => 'Breathable chambray with a soft hand feel. Dress it up or wear it open over a tee.'],
        ['name' => 'Distressed Denim Shorts', 'category' => 'Shorts', 'price' => 1299, 'sizes' => ['S', 'M', 'L', 'XL'], 'colors' => ['Blue', 'Light Blue'],
            'description' => 'Knee-length denim shorts with light distressing and a comfortable relaxed cut.'],
    ];

    public function run(): void
    {
        foreach (self::PRODUCTS as $index => $data) {
            $slug = Str::slug($data['name']);

            // Safe to re-run: products that already exist are left untouched.
            if (Product::withTrashed()->where('slug', $slug)->exists()) {
                continue;
            }

            $category = Category::firstOrCreate(['slug' => Str::slug($data['category'])], ['name' => $data['category']]);

            DB::transaction(function () use ($data, $slug, $index, $category) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'slug' => $slug,
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'status' => 'active',
                    'stock' => 0,
                ]);

                foreach ($data['sizes'] as $sizeIndex => $size) {
                    foreach ($data['colors'] as $colorIndex => $color) {
                        // Deterministic spread of 0-9 so demos show in-stock, low-stock and sold-out variants.
                        $product->variants()->create([
                            'size' => $size,
                            'color' => $color,
                            'stock' => ($sizeIndex * 3 + $colorIndex * 2 + $index * 5 + 4) % 10,
                        ]);
                    }
                }

                $product->update(['stock' => (int) $product->variants()->sum('stock')]);

                $this->createImages($product, $data['colors']);
            });
        }
    }

    /** @param  list<string>  $colors */
    private function createImages(Product $product, array $colors): void
    {
        foreach (array_slice($colors, 0, 2) as $position => $color) {
            $path = "products/seed-{$product->slug}-".($position + 1).'.svg';
            $label = $position === 0 ? $product->name : $product->name.' - detail';

            Storage::disk('public')->put($path, $this->svg($label, self::COLOR_HEX[$color] ?? '#1f3a5f'));
            $product->images()->create(['image' => $path]);
        }
    }

    /** Local placeholder artwork, so the demo never depends on external image hosts. */
    private function svg(string $label, string $background): string
    {
        $lines = explode("\n", wordwrap($label, 20, "\n", true));
        $startY = 300 - (count($lines) - 1) * 24;
        $text = '';

        foreach ($lines as $i => $line) {
            $y = $startY + $i * 48;
            $text .= '<text x="300" y="'.$y.'" text-anchor="middle" font-family="Georgia, serif" font-size="36" fill="#ffffff">'
                .htmlspecialchars($line, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</text>';
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" role="img">
<defs><pattern id="weave" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M0 8L8 0" stroke="#ffffff" stroke-opacity=".09" stroke-width="2"/></pattern></defs>
<rect width="600" height="600" fill="{$background}"/>
<rect width="600" height="600" fill="url(#weave)"/>
<rect x="28" y="28" width="544" height="544" fill="none" stroke="#c9a24d" stroke-width="3" stroke-dasharray="14 10"/>
{$text}
<text x="300" y="540" text-anchor="middle" font-family="Georgia, serif" font-size="20" letter-spacing="4" fill="#c9a24d">INDIGO &amp; CO.</text>
</svg>
SVG;
    }
}
