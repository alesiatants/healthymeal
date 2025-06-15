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
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('goal', ['Набрать вес', 'Поддержать вес', 'Сбросить вес'])->default('Поддержать вес');
            $table->enum('activity_level', ['Умственный', 'Лёгкий', 'Средний',  'Тяжёлый', 'Сверхтяжёлый'])->default('Лёгкий');
            $table->decimal('weight', 5, 1);
            $table->integer('meals_per_day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_plans');
    }
};
