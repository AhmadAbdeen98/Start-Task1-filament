<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a paginated listing of products.
     *
     * Supports: search, filtering by category and status, sorting, pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->with('category');

        $query->search($request->get('search'));

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        if (in_array($sortField, ['name', 'price', 'stock_quantity', 'created_at'], true)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $products = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        $product->load('category');

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        $product->load('category');

        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }
}
