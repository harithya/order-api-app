<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $this->productService->all(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $this->productService->store($request->validated()),
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $this->productService->find($id),
        ]);
    }

    public function update(ProductRequest $request, string $id)
    {
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $this->productService->update($request->validated(), $id),
        ]);
    }

    public function destroy(string $id)
    {
        $this->productService->delete($id);

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
