<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(ProductFilterRequest $request): JsonResponse
    {
        $products = Product::query()
            ->active()
            ->with(['category', 'images', 'variants'])
            ->filteredBy($request->safe()->except(['page', 'per_page']))
            ->paginate($request->integer('per_page') ?: 12);

        return ApiResponse::success(
            'Products fetched successfully',
            ProductResource::collection($products->getCollection())->resolve(),
            meta: [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        );
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::query()
            ->active()
            ->with(['category', 'images', 'variants'])
            ->findOrFail($id);

        return ApiResponse::success('Product fetched successfully', (new ProductResource($product))->resolve());
    }
}
