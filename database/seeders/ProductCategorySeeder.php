<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Protein', 'Vitamins', 'Supplements', 'Herbal Products'];

        foreach ($categories as $category) {
            ProductCategory::create(['name' => $category]);
        }
    }
}
