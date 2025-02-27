<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade'); // Foreign key reference to categories
            $table->string('name', 255); // Product Name
            $table->decimal('mrp', 10, 2); // MRP (Maximum Retail Price)
            $table->string('weight', 50); // Product Weight
            $table->integer('svp_points')->default(0); // SVP Points
            $table->integer('stock')->default(0); // Stock quantity
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
