<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateProductRequest",
 *     type="object",
 *     required={"name", "category_id", "status", "quantity", "price", "weight","image_url"},
 *     @OA\Property(property="name", type="string", example="Protein Shake", description="Unique product name"),
 *     @OA\Property(property="category_id", type="integer", example=1, description="Must exist in the categories table"),
 *     @OA\Property(property="status", type="integer", enum={0,1}, example=1, description="0 for inactive, 1 for active"),
 *     @OA\Property(property="quantity", type="string", example=50, description="Stock quantity"),
 *     @OA\Property(property="price", type="number", format="float", example=299.99, description="Product price"),
 *     @OA\Property(property="weight", type="string", example="500g", description="Product weight"),
 *     @OA\Property(property="image_url", type="string", example="https://image.jpeg", description="Product image"),
 * )
 */
class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products')->ignore($this->product)
            ],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'status' => 'sometimes|in:0,1',
            'image_url' => 'sometimes|string',
            'quantity' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'weight' => 'sometimes|string',
        ];
    }
} 