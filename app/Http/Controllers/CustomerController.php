<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CustomPaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends ApiController
{
    use CustomPaginationTrait;

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Create a new customer",
     *     description="Registers a new customer with a default role of 'customer'. Requires authentication.",
     *     operationId="Add/Create Customer",
     *     tags={"Customers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Customer created successfully"),
     *             @OA\Property(property="status", type="integer", example=201),
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
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Token missing or invalid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'mobile_number' => 'required|string|unique:users,mobile_no|max:15', // New field
                'password' => 'required|string|min:6',
            ]);

            $customer = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'customer', // Default role
            ]);

            return response()->json([
                'message' => 'Customer created successfully',
                'status' => 201,
                'data' => $customer,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Get all customers",
     *     description="Fetches all customers from the database. Requires authentication.",
     *     operationId="getCustomers",
     *     tags={"Customers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter customers by status. Allowed values: active, inactive",
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customers Fetched Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Students Fetched Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Token missing or invalid",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - User does not have permission",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active(); // Using the local scope
            } elseif ($request->status === 'inactive') {
                $query->where(function ($q) {
                    $q->where('status', '!=', 1);
                });
            }
        }

        $customers = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'message' => 'Customer Fetched Successfully',
            'status' => 200,
            'data' => CustomerResource::collection($customers),
            'pagination' => self::buildPagination($customers)
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{customer}",
     *     summary="Get a specific customer",
     *     description="Fetches details of a specific customer by ID.",
     *     operationId="getCustomer",
     *     tags={"Customers"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="customer",
     *         in="path",
     *         required=true,
     *         description="ID of the customer to fetch",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student Fetched Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CustomerResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
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
    public function show($customer): JsonResponse
    {
        $user = User::find($customer);

        if (! $user) {
            return response()->json(['message' => 'Customer not found', 'status' => 404], 404);
        }

        return response()->json([
            'message' => 'Customer details fetched successfully',
            'status' => 200,
            'data' => new CustomerResource($user),
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/customers/{customer}",
     *     summary="Delete a customer",
     *     description="Deletes a customer by ID",
     *     operationId="deleteCustomer",
     *     tags={"Customers"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="customer",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer deleted successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer not found"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function delete(User $customer): JsonResponse
    {
        if (!$customer) {
            return response()->json(['message' => 'Customer not found', 'status' => 404], 404);
        }
        // Delete the customer
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully', 'status' => 200], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{customer}",
     *     summary="Update customer details",
     *     description="Updates an existing customer's information",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customer",
     *         in="path",
     *         required=true,
     *         description="ID of customer to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateCustomerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer Updated Successfully"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/CustomerResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer not found"),
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
    public function update(UpdateCustomerRequest $request, User $customer)
    {
        try {
            $customer->update([
                'name' => $request->name ?? $customer->name,
                'email' => $request->email ?? $customer->email,
                'mobile_no' => $request->mobile_number ?? $customer->mobile_no,
                'address' => $request->address ?? $customer->address,
                'status' => $request->status ?? $customer->status
            ]);

            return response()->json([
                'message' => 'Customer Updated Successfully',
                'status' => 200,
                'data' => new CustomerResource($customer->fresh())
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update customer',
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
