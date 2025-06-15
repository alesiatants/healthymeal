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
        Schema::create('recipes_steps', function (Blueprint $table) {
            $table->id();
			$table->foreignId('recipes_id')->constrained('recipes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('step_number');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes_steps');
    }
};
