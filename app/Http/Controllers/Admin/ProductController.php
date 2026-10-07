<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $products)
    {
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->search($this->stringQuery($request, 'search'))
            ->inCategory($request->integer('category') ?: null)
            ->withStatus($this->stringQuery($request, 'status'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->products->create($request->validated(), $request->file('images', []));

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product->load(['images', 'variants']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->products->update($product, $request->validated(), $request->file('images', []));

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function stringQuery(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) ? $value : null;
    }
}
