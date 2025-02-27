<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerController extends ApiController
{
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
        $customers = User::all();
        return response()->json([
            'message' => 'Customer Fetched Successfully',
            'status' => 200,
            'data' => CustomerResource::collection($customers),
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
}
