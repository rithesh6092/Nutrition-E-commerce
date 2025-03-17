<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     description="Allows users to add a new product category.",
     *     operationId="createCategory",
     *     tags={"Product Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "status"},
     *             @OA\Property(property="name", type="string", example="Protein"),
     *             @OA\Property(property="status", type="integer", enum={0,1}, example=1, description="Product status: 1 for active, 0 for inactive.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="data", ref="#/components/schemas/CategoryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = ProductCategory::create($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'status' => 201,
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all product categories",
     *     description="Fetches a list of all categories.",
     *     operationId="getCategories",
     *     tags={"Product Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Categories fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Categories Fetched Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CategoryResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $categories = ProductCategory::all();
        return response()->json([
            'message' => 'Categories Fetched Successfully',
            'status' => 200,
            'data' => CategoryResource::collection($categories),
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     description="Updates an existing category by ID. The name must be unique but ignores the current category's ID.",
     *     operationId="updateCategory",
     *     tags={"Product Categories"},
     *     security={{ "bearerAuth":{} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "status"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Weight Management"),
     *             @OA\Property(property="status", type="integer", enum={0,1}, example=1, description="1=Active, 0=Inactive")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category Updated Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category Updated Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="name", type="string", example="Weight Management"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-24T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-02-24T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Category Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="status", type="integer", example=422),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="Category name is already taken."))
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $category): JsonResponse
    {
        $category = ProductCategory::find($category);

        if (! $category) {
            return response()->json(['message' => 'Category not found', 'status' => 404], 404);
        }

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name')->ignore($category->id),
            ],
            'status' => 'required|in:1,0',
        ]);

        // Update the category
        $category->update($validatedData);

        return response()->json([
            'message' => 'Category Updated Successfully',
            'status' => 200,
            'data' => $category,
        ], 200);
    }

    public function updateCategoryStatus(ProductCategory $category, Request $request)
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

        $category->update(['status' => $validated['status']]);
        return response()->json([
            'message' => 'Category status updated successfully',
            'status' => 200,
            'data' => new CategoryResource($category->fresh()),
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/active-categories",
     *     summary="Get all Active categories",
     *     description="Fetches a list of active categories.",
     *     operationId="getActiveCategories",
     *     tags={"Web API's"},
     *     @OA\Response(
     *         response=200,
     *         description="Categories fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Categories Fetched Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CategoryResource")
     *             )
     *         )
     *     )
     * )
     */
    public function webCategories(Request $request): JsonResponse
    {
        $query = ProductCategory::active();

        $categories = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'message' => 'Categories Fetched Successfully',
            'status' => 200,
            'data' => CategoryResource::collection($categories),
            // 'pagination' => self::buildPagination($categories, 'categories')
        ], 200);
    }
}
