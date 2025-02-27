<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Nutrition E-commerce API Documentation",
 *      description="Apis to perform various actions on resources",
 *
 *      @OA\Contact(
 *          email="support@email.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Test API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based authentication",
 *     in="header",
 *     name="apiKey",
 *     scheme="bearer",
 *     securityScheme="bearerAuth",
 * )
 */

class ApiController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return a success response.
     */
    protected function successResponse($data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Return an error response.
     */
    protected function errorResponse(string $message, int $status = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
