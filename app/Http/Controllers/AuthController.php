<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    /**
     * Handle an incoming authentication request.
     */

    /**
     * @OA\Post(
     *     path="/api/v1/admin-login",
     *     summary="Admin Login",
     *     tags={"Admin"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="admin@gmail.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login success.",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login success."),
     *             @OA\Property(property="status", type="integer", example="200"),
     *             @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                     property="access_token", type="string"
     *                  )
     *             )
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid credentials.",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login success."),
     *             @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                     property="email",
     *                     type="array",
     *
     *                     @OA\Items(type="string", example="The provided credentials are incorrect.")
     *                  )
     *             )
     *         ),
     *     )
     * )
     */
    public function adminLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
        ]);

        $admin = User::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Invalid Email',
                'status' => 401,
            ], 401);
        }

        if ($admin->status !== 'active') {
            return response()->json([
                'message' => 'Inactive account',
                'status' => 403, // Use 403 Forbidden for inactive accounts
            ], 403);
        }


        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => 401,
            ], 401);
        }

        if (!Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return response()->json([
                'message' => 'Authentication failed',
                'status' => 401,
            ], 401);
        }

        $token = $admin->createToken('AdminToken')->plainTextToken;
    
        return response()->json([
            'message' => 'Logged in successfully.',
            'status' => 200,
            'access_token' => $token,
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/admin",
     *     summary="Create a new admin",
     *     tags={"Admin"},
     *     description="This API creates a new admin with the provided details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Super Admin"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin Created Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin Created Successfully"),
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="admin", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Super Admin"),
     *                     @OA\Property(property="email", type="string", example="admin@example.com"),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 ),
     *                 @OA\Property(property="access_token", type="string", example="your-generated-token")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The email field is required."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required."))
     *             )
     *         )
     *     )
     * )
     */
    // public function createAdmin(Request $request): JsonResponse
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:admins,email',
    //         'password' => 'required|string|min:8|confirmed',
    //     ], [
    //         'name.required' => 'Name is required',
    //         'email.required' => 'Email is required',
    //         'email.unique' => 'Email already exists',
    //         'password.required' => 'Password is required',
    //         'password.min' => 'Password must be at least 8 characters',
    //         'password.confirmed' => 'Passwords do not match',
    //     ]);

    //     $admin = Admin::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'status' => 'active',
    //     ]);

    //     if (method_exists($admin, 'assignRole')) {
    //         $admin->assignRole('admin');
    //     }

    //     $token = $admin->createToken('AdminToken')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Admin Created Successfully',
    //         'status' => 201,
    //         'data' => [
    //             'admin' => $admin,
    //             'access_token' => $token,
    //         ],
    //     ], 201);
    // }

    // public function store(Request $request)
    // {
    //     $rules = [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ];

    //     $customMessages = [
    //         'email.required' => __('Email is required'),
    //         'password.required' => __('Password is required'),
    //     ];
    //     $this->validate($request, $rules, $customMessages);

    //     $credential = [
    //         'email' => $request->email,
    //         'password' => $request->password,
    //     ];

    //     $admin = Admin::where('email', $request->email)->first();

    //     if ($admin) {
    //         if ($admin->status == 'active') {
    //             if (Hash::check($request->password, $admin->password)) {
    //                 if (Auth::guard('admin')->attempt($credential, $request->remember)) {
    //                     $notification = __('Logged in successfully.');
    //                     $notification = ['messege' => $notification, 'alert-type' => 'success'];

    //                     return redirect()->route('admin.dashboard')->with($notification);
    //                 }
    //             } else {
    //                 $notification = __('Invalid Password');
    //                 $notification = ['messege' => $notification, 'alert-type' => 'error'];

    //                 return redirect()->back()->with($notification);
    //             }
    //         } else {
    //             $notification = __('Inactive account');
    //             $notification = ['messege' => $notification, 'alert-type' => 'error'];

    //             return redirect()->back()->with($notification);
    //         }
    //     } else {
    //         $notification = __('Invalid Email');
    //         $notification = ['messege' => $notification, 'alert-type' => 'error'];

    //         return redirect()->back()->with($notification);
    //     }

    // }
}
