<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get random users and products (adjust based on your setup)
        $users = User::pluck('id')->toArray();
        $products = Product::pluck('id')->toArray();

        for ($i = 0; $i < 10; $i++) { // Create 50 fake reviews
            Review::create([
                'user_id' => $faker->randomElement($users),
                'product_id' => $faker->randomElement($products),
                'rating' => $faker->numberBetween(1, 5),
                'comment' => $faker->sentence(10),
            ]);
        }
    }
}
