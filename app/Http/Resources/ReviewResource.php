<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Helpers\ProfileHelper;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ReviewResource",
 *     description="Review resource representation",
 *     @OA\Xml(name="ReviewResource")
 * )
 */
class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

      /**
     * @OA\Property(property="id", type="integer", example=1, description="Review ID"),
     * @OA\Property(property="rating", type="integer", example=5, description="Rating for the product"),
     * @OA\Property(property="profile_image", type="string", example="https://profile.jpeg", description="Customer profile image"),
     * @OA\Property(property="comment", type="string", example="review content", description="review content"),
     * @OA\Property(property="name", type="string", example="John Doe", description="Customer Name"),
     * @OA\Property(property="created_at", type="string", example="15 minutes ago", description="Review posted date of product"),
     * 
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'profile_image' => $this->customer?->profile_image ?? ProfileHelper::generateAvatarUrl($this->customer?->name),
            'comment' => $this->comment,
            'name' => $this->customer?->name,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
