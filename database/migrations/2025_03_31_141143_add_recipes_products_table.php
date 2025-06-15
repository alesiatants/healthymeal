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
        Schema::create('recipes_products', function (Blueprint $table) {
            $table->id();
			$table->foreignId('recipes_id')->constrained('recipes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('products_id')->constrained('products')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(["recipes_id", "products_id"], 'recipe_product');
            $table->float('quantity');
            $table->string('unit');
            $table->string('grams');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes_products');
    }
};
