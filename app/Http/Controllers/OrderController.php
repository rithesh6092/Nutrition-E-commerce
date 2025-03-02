<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomPaginationTrait;
use App\Http\Resources\OrderResource;


class OrderController extends Controller
{
    use CustomPaginationTrait;

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get list of orders",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="customer_id", type="integer"),
     *                     @OA\Property(property="amount", type="number", format="decimal"),
     *                     @OA\Property(property="status", type="string", enum={"pending", "processing", "completed", "cancelled"}),
     *                     @OA\Property(property="payment_status", type="string", enum={"pending", "paid", "failed"}),
     *                     @OA\Property(property="items", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="product_id", type="integer"),
     *                             @OA\Property(property="quantity", type="integer"),
     *                             @OA\Property(property="unit_price", type="number"),
     *                             @OA\Property(property="subtotal", type="number")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $orders = Order::with(['customer', 'items.product'])->latest()->paginate($request->per_page ?? 10);
        
        return response()->json([
            'message' => 'Orders fetched successfully',
            'status' => 200,
            'data' => OrderResource::collection($orders),
            'pagination' => self::buildPagination($orders, 'orders')
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/orders/{order}",
     *     summary="Get order details",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="payment_status", type="string"),
     *             @OA\Property(property="items", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="order_id", type="integer"),
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="quantity", type="integer"),
     *                     @OA\Property(property="unit_price", type="number"),
     *                     @OA\Property(property="subtotal", type="number"),
     *                     @OA\Property(
     *                         property="product",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="price", type="number")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product']);
        return response()->json($order);
    }
}
