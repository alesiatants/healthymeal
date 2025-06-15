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
       // Сначала добавляем столбец без уникальности
       Schema::table('product_types', function (Blueprint $table) {
        $table->string('slug')->nullable()->after('type');
    });

    // Заполняем slug для существующих записей
    $types = DB::table('product_types')->get();
    foreach ($types as $type) {
        DB::table('product_types')
            ->where('id', $type->id)
            ->update(['slug' => Str::slug($type->type)]);
    }

    // Затем изменяем столбец, делая его уникальным и не nullable
    Schema::table('product_types', function (Blueprint $table) {
        $table->string('slug')->unique()->nullable(false)->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
