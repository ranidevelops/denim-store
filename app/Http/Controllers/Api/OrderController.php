<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $quantities = $this->orders->variantQuantities($request->validated('items'));

        try {
            $order = $this->orders->place(
                $request->user(),
                $request->safe()->only(['customer_name', 'phone', 'shipping_address']),
                $quantities,
            );
        } catch (ValidationException $e) {
            throw ValidationException::withMessages(['items' => Arr::flatten($e->errors())]);
        }

        return ApiResponse::success(
            'Order placed successfully',
            (new OrderResource($order->load('items')))->resolve(),
            201,
        );
    }
}
