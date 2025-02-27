<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => rand(1, 5),
            'name' => $this->faker->unique()->word() . ' ' . $this->faker->randomElement(['Protein', 'Capsules', 'Shake']),
            'mrp' => $this->faker->randomFloat(2, 500, 5000),
            'weight' => rand(1, 5) . 'kg',
            'svp_points' => rand(5, 25),
            'stock' => rand(10, 100),
            'status' => rand(0, 1),
        ];
    }
}
