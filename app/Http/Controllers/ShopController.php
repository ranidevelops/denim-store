<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(ProductFilterRequest $request): View|JsonResponse
    {
        $filters = collect($request->safe()->except(['page', 'per_page']))
            ->filter(fn ($value) => $value !== null && $value !== '');

        $products = Product::query()
            ->active()
            ->with(['category', 'images', 'variants'])
            ->filteredBy($filters->all())
            ->paginate(12)
            ->withQueryString();

        $hasFilters = $filters->except('sort')->isNotEmpty();

        // Filter form and pagination refresh just the results via fetch().
        if ($request->ajax()) {
            return response()->json([
                'html' => view('shop._results', ['products' => $products, 'showHero' => false])->render(),
                'has_filters' => $hasFilters,
            ]);
        }

        return view('shop.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'sizes' => config('denim.sizes'),
            'colors' => config('denim.colors'),
            'sorts' => ProductFilterRequest::SORTS,
            'filters' => $filters,
            'hasFilters' => $hasFilters,
            'showHero' => $request->routeIs('home') && $request->query() === [],
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->status === ProductStatus::Active, 404);

        $product->load(['category', 'images', 'variants']);

        $related = Product::query()
            ->active()
            ->with(['category', 'images', 'variants'])
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->latest()
            ->limit(4)
            ->get();

        return view('shop.show', ['product' => $product, 'related' => $related]);
    }
}
