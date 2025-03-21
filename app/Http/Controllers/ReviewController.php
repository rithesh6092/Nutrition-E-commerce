<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Product;
use App\Traits\CustomPaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ReviewController extends ApiController
{
    use CustomPaginationTrait;

     /**
     * @OA\Get(
     *     path="/api/reviews",
     *     tags={"Reviews"},
     *     summary="List of reviews",
     *
     *     @OA\Parameter(
     *       name="sort_by",
     *       in="query",
     *       description="Sort either by asc or desc",
     *       required=false
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Reviews Fetched Successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example="200"),
     *             @OA\Property(
     *                  property="data",
     *                  type="array",
     *
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/ReviewResource"
     *                  )
     *             )
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated logout",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status", type="integer", example="401")
     *         ),
     *     ),
     *     security={{"bearerAuth":{}}},
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $reviews = Review::with('customer')->paginate($request->per_page ?? 5);
        return response()->json([
            'message' => 'Reviews Fetched Successfully',
            'status' => 200,
            'reviews' => ReviewResource::collection($reviews),
            'pagination' => self::buildPagination($reviews),
        ], 200);
    }

    /**
    * @OA\Get(
    *     path="/api/product-reviews/{productId}",
    *     summary="Get reviews for a specific product",
    *     description="Fetch paginated reviews for a given product ID.",
    *     operationId="getProductReviews",
    *     tags={"Web API's"},
    *     
    *     @OA\Parameter(
    *         name="productId",
    *         in="path",
    *         required=true,
    *         description="The ID of the product",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Parameter(
    *         name="per_page",
    *         in="query",
    *         required=false,
    *         description="Number of reviews per page (default is 5)",
    *         @OA\Schema(type="integer", example=10)
    *     ),
    *     @OA\Parameter(
    *         name="page",
    *         in="query",
    *         description="Page number for pagination",
    *         required=false,
    *         @OA\Schema(type="integer", default=1)
    *     ),
    *     
    *     @OA\Response(
    *         response=200,
    *         description="Product Reviews Fetched Successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Product Reviews Fetched Successfully"),
    *             @OA\Property(property="status", type="integer", example=200),
    *             @OA\Property(
    *                 property="reviews",
    *                 type="array",
    *                 @OA\Items(ref="#/components/schemas/ReviewResource")
    *             ),
    *         @OA\Property(
    *                 property="pagination",
    *                 allOf={
    *                     @OA\Schema(ref="#/components/schemas/PaginationResource")
    *                 }
    *             )
    *         )
    *     ),
    *     
    *     @OA\Response(
    *         response=404,
    *         description="No reviews found for this product",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="No reviews found for this product"),
    *             @OA\Property(property="status", type="integer", example=404),
    *             @OA\Property(property="reviews", type="array", @OA\Items()),
    *             @OA\Property(property="pagination", type="null")
    *         )
    *     )
    * )
    */
    public function getProductReviews(Request $request, $productId): JsonResponse
    {
        // Fetch reviews for the given product ID with pagination
       $productReviews = Review::where('product_id', $productId)
                            ->with('customer') // Load related user data
                            ->paginate($request->per_page ?? 5);
       // Check if reviews exist
       if ($productReviews->isEmpty()) {
            return response()->json([
                'message' => 'No reviews found for this product',
                'status' => 404,
                'reviews' => [],
                'pagination' => null,
            ], 404);
        }
        
        return response()->json([
            'message' => 'Product Reviews Fetched Successfully',
            'status' => 200,
            'reviews' => ReviewResource::collection($productReviews),
            'pagination' => self::buildPagination($productReviews),
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/top-rated-products",
     *     summary="Get top-rated products",
     *     description="Fetches top-rated products with 5-star reviews, including pagination.",
     *     operationId="getTopRatedProducts",
     *     tags={"Web API's"},
     * 
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Top-rated products fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Top-rated products fetched successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductResource")
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/PaginationResource")
     *                 }
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="No top-rated products found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No top-rated products found"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="products", type="array", @OA\Items()),
     *             @OA\Property(property="pagination", type="null")
     *         )
     *     )
     * )
     */
    public function getTopRatedProducts(Request $request): JsonResponse
    {
        $topRatedProducts = Product::whereHas('reviews')
        ->with(['reviews'])
        ->paginate($request->per_page ?? 10);
        
        // Check if there are any top-rated products
        if ($topRatedProducts->isEmpty()) {
                return response()->json([
                    'message' => 'No top-rated products found',
                    'status' => 404,
                    'products' => [],
                    'pagination' => null,
                ], 404);
        }
        
        return response()->json([
            'message' => 'Top-rated products fetched successfully',
            'status' => 200,
            'products' => ProductResource::collection($topRatedProducts),
            'pagination' => self::buildPagination($topRatedProducts),
        ], 200);
    }
}
