<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_types')->insert([
            ['type' => 'Мясо', 'slug' => Str::slug('Мясо')],
            ['type' => 'Рыба', 'slug' => Str::slug('Рыба')],
            ['type' => 'Морепродукты', 'slug' => Str::slug('Морепродукты')],
            ['type' => 'Яйца', 'slug' => Str::slug('Яйца')],
            ['type' => 'Молоко и молочные продукты', 'slug' => Str::slug('Молоко и молочные продукты')],
            ['type' => 'Соя и соевые продукты', 'slug' => Str::slug('Соя и соевые продукты')],
            ['type' => 'Фрукты, ягоды, сухофрукты', 'slug' => Str::slug('Фрукты, ягоды, сухофрукты')],
            ['type' => 'Зелень, травы, листья, салаты', 'slug' => Str::slug('Зелень, травы, листья, салаты')],
            ['type' => 'Грибы', 'slug' => Str::slug('Грибы')],
            ['type' => 'Жиры, масла', 'slug' => Str::slug('Жиры, масла')],
            ['type' => 'Орехи', 'slug' => Str::slug('Орехи')],
            ['type' => 'Крупы, злаки', 'slug' => Str::slug('Крупы, злаки')],
            ['type' => 'Сладости, кондитерские изделия', 'slug' => Str::slug('Сладости, кондитерские изделия')],
            ['type' => 'Напитки', 'slug' => Str::slug('Напитки')],
            ['type' => 'Семена', 'slug' => Str::slug('Семена')],
            ['type' => 'Специи, пряности', 'slug' => Str::slug('Специи, пряности')],
            ['type' => 'Мучные продукты', 'slug' => Str::slug('Мучные продукты')]
        ]);
    }
}
