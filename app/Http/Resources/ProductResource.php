<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @OA\Schema(
 *     title="ProductResource",
 *     description="Product resource representation",
 *     @OA\Xml(name="ProductResource")
 * )
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    /**
     * @OA\Property(property="id", type="integer", example=1, description="Product ID"),
     * @OA\Property(property="product_name", type="string", example="Whey Protein", description="Product Name"),
     * @OA\Property(property="price", type="number", format="float", example=2999.99, description="Product Price (MRP)"),
     * @OA\Property(property="weight", type="string", example="2kg", description="Product Weight"),
     * @OA\Property(property="quantity", type="integer", example=50, description="Available Stock Quantity"),
     * @OA\Property(property="svp_points", type="integer", example=15, description="SVP Points Earned"),
     * @OA\Property(property="status", type="string", enum={"active", "in-active"}, example="active", description="Product Status"),
     * @OA\Property(property="addedOn", type="string", format="date", example="26-02-2025", description="Product Added Date"),
     * 
     * @OA\Property(
     *     property="category",
     *     type="object",
     *     nullable=true,
     *     description="Product category details",
     *     @OA\Property(property="id", type="integer", example=5, description="Category ID"),
     *     @OA\Property(property="name", type="string", example="Supplements", description="Category Name")
     * )
     */

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this?->name,
            'price' => $this->mrp,
            'weight' => $this->weight,
            'quantity' => $this->stock,
            'svp_points' => $this->svp_points,
            'status' => $this->status == 1 ? 'active' : 'in-active',
            'addedOn' => $this->created_at->format('d-m-Y'),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
        ];
    }
}
