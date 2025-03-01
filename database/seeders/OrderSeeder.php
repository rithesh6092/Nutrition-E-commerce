<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 orders
        for ($i = 0; $i < 10; $i++) {
            // Create order with random customer
            $order = Order::create([
                'customer_id' => rand(1, 4),
                'amount' => 0, // Will be calculated based on items
                'status' => collect(['pending', 'processing', 'completed', 'cancelled'])->random(),
                'payment_status' => collect(['pending', 'paid', 'failed'])->random(),
            ]);

            // Create 1-3 order items for each order
            $totalAmount = 0;
            $numberOfItems = rand(1, 3);

            for ($j = 0; $j < $numberOfItems; $j++) {
                $product = Product::find(rand(1, 5));
                $quantity = rand(1, 5);
                $subtotal = $product->mrp * $quantity;
                $totalAmount += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->mrp,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update order total amount
            $order->update(['amount' => $totalAmount]);
        }
    }
} 