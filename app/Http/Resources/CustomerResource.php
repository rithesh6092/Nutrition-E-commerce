<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CustomerResource",
 *     title="Customer Resource",
 *     description="Customer data structure",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-24T12:34:56Z"),
 *     @OA\Property(property="registered_on", type="string", example="2024-02-24")
 * )
 */
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this?->name,
            'email' => $this->email,
            'mobile' => $this->mobile_no,
            'role' => $this->role,
            'status' => $this->status == 1 ? 'active' : 'in-active',
        ];
    }
}
