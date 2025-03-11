<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\CustomPaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ProductHelper;

class ProductController extends Controller
{
    use CustomPaginationTrait;

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     description="Creates a new product and returns the created product details",
     *     operationId="storeProduct",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product Created Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product Created Successfully"),
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="status", type="integer", example=422),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'stock' => $request->quantity,
            'svp_points' => ProductHelper::calculateSVPPoints($request->price),
            'mrp' => $request->price,
            'weight' => $request->weight,
            'image_url' => $request->image_url,
        ]);

        return response()->json([
            'message' => 'Product Created Successfully',
            'status' => 201,
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     description="Fetches a paginated list of all available products",
     *     operationId="getProducts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter products by status. Allowed values: active, inactive",
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products Fetched Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Products Fetched Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/ProductResource")
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/PaginationResource")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        // $perPage = $request->input('per_page', 10);

        // $products = Product::with('category')->paginate($perPage);
        $query = Product::query()->with('category');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active(); // Using the local scope
            } elseif ($request->status === 'inactive') {
                $query->where(function ($q) {
                    $q->where('status', '!=', 1);
                });
            }
        }

        $products = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'message' => 'Products Fetched Successfully',
            'status' => 200,
            'data' => ProductResource::collection($products),
            'pagination' => self::buildPagination($products, 'products')
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{product}",
     *     summary="Get a specific product",
     *     description="Fetches details of a specific product by ID.",
     *     operationId="getProduct",
     *     tags={"Products"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="ID of the product to fetch",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student Fetched Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No Student found"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function show($product): JsonResponse
    {
        $user = Product::with(['category:id,name'])->find($product);

        if (! $user) {
            return response()->json(['message' => 'Product not found', 'status' => 404], 404);
        }

        return response()->json([
            'message' => 'Product details fetched successfully',
            'status' => 200,
            'data' => new ProductResource($user),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{product}",
     *     summary="Update a product",
     *     description="Updates an existing product's information",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="ID of product to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product Updated Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="status", type="integer", example=422),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $product->update([
                'name' => $request->name ?? $product->name,
                'category_id' => $request->category_id ?? $product->category_id,
                'status' => $request->status ?? $product->status,
                'stock' => $request->quantity ?? $product->stock,
                'svp_points' => $request->price ? ProductHelper::calculateSVPPoints($request->price) : $product->svp_points,
                'mrp' => $request->price ?? $product->mrp,
                'weight' => $request->weight ?? $product->weight,
                'image_url' => $request->image_url ?? $product->image_url,
            ]);

            return response()->json([
                'message' => 'Product Updated Successfully',
                'status' => 200,
                'data' => new ProductResource($product->fresh(['category']))
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update product',
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete() {}

    /**
     * @OA\Put(
     *     path="/api/products/status/{product}",
     *     summary="Update product status",
     *     description="Updates the status of a product to either active (1) or inactive (0).",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="integer", enum={0,1}, example=1, description="Product status: 1 for active, 0 for inactive.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Product Name"),
     *             @OA\Property(property="status", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The status field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found.")
     *         )
     *     )
     * )
     */
    public function updateProductStatus(Product $product, Request $request)
    {
        $validated = $request->validate(
            [
                'status' => 'required|in:0,1',
            ],
            [
                'status.required' => 'status is required.',
                'status.in' => 'status must be either 1 or 0.',
            ]
        );

        $product->update(['status' => $validated['status']]);
        return response()->json([
            'message' => 'Product status updated successfully',
            'status' => 200,
            'data' => new ProductResource($product),
        ], 200);
    }
}
