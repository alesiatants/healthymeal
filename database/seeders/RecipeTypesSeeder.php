<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecipeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('recipe_types')->insert([
            ['type' => 'Напитки', 'slug' => Str::slug('Напитки')],
            ['type' => 'Закуски', 'slug' => Str::slug('Закуски')],
            ['type' => 'Первые блюда', 'slug' => Str::slug('Первые блюда')],
            ['type' => 'Вторые блюда', 'slug' => Str::slug('Вторые блюда')],
            ['type' => 'Гарниры', 'slug' => Str::slug('Гарниры')],
            ['type' => 'Консервы и заготовки', 'slug' => Str::slug('Консервы и заготовки')],
            ['type' => 'Соусы и маринады', 'slug' => Str::slug('Соусы и маринады')],
            ['type' => 'Выпечка', 'slug' => Str::slug('Выпечка')],
            ['type' => 'Десерты', 'slug' => Str::slug('Десерты')],
            ['type' => 'Салаты', 'slug' => Str::slug('Салаты')],
        ]);
    }
}
