<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $this->orderService->all(),
        ]);
    }

    public function store(OrderRequest $request)
    {
        return response()->json([
            'message' => 'Order created successfully',
            'data' => $this->orderService->store($request->validated()),
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $this->orderService->find($id),
        ]);
    }
}
