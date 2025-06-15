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
        Schema::create('product_types_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category')->unique();
            $table->foreignId('product_type_id')->constrained('product_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_types_categories');
    }
};
