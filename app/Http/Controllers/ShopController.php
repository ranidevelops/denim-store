<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(ProductFilterRequest $request): View
    {
        $filters = collect($request->safe()->except('page'))
            ->filter(fn ($value) => $value !== null && $value !== '');

        $products = Product::query()
            ->active()
            ->with(['category', 'images', 'variants'])
            ->search($filters->get('search'))
            ->inCategorySlug($filters->get('category'))
            ->availableIn($filters->get('size'), $filters->get('color'))
            ->priceBetween($filters->get('min_price'), $filters->get('max_price'))
            ->sortedBy($filters->get('sort'))
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'sizes' => config('denim.sizes'),
            'colors' => config('denim.colors'),
            'sorts' => ProductFilterRequest::SORTS,
            'filters' => $filters,
            'hasFilters' => $filters->except('sort')->isNotEmpty(),
            'showHero' => $request->routeIs('home') && $request->query() === [],
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->status === ProductStatus::Active, 404);

        $product->load(['category', 'images', 'variants']);

        return view('shop.show', ['product' => $product]);
    }
}
