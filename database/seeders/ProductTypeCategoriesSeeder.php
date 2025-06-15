<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductType;
use App\Models\ProductTypesCategories;
use Illuminate\Support\Str;

class ProductTypeCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = ProductType::where('type', 'Мясо')->first();
		$category = ProductTypesCategories::create([
			'category' => 'Мясо убойных животных',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Мясо убойных животных')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Мясо диких животных',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Мясо диких животных')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Мясо птицы',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Мясо птицы')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Субродукты',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Субродукты')
		]);
        $type = ProductType::where('type', 'Морепродукты')->first();
		$category = ProductTypesCategories::create([
			'category' => 'Моллюски',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Морепродукты')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Ракообразные',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Ракообразные')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Морские водоросли',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Морские водоросли')
		]);

        $type = ProductType::where('type', 'Молоко и молочные продукты')->first();
        $category = ProductTypesCategories::create([
			'category' => 'Сыры',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Сыры')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Кисломолочные продукты',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Кисломолочные продукты')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Творог',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Творог')
		]);

        $type = ProductType::where('type', 'Фрукты, ягоды, сухофрукты')->first();
        $category = ProductTypesCategories::create([
			'category' => 'Фрукты',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Фрукты')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Ягоды',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Ягоды')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Сухофрукты',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Сухофрукты')
		]);

        $type = ProductType::where('type', 'Мучные продукты')->first();
        $category = ProductTypesCategories::create([
			'category' => 'Мука',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Мука')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Хлеб',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Хлеб')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Макароны',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Макароны')
		]);
        $type = ProductType::where('type', 'Напитки')->first();
        $category = ProductTypesCategories::create([
			'category' => 'Соки и нектары',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Соки и нектары')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Алкоголь',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Алкоголь')
		]);
        $category = ProductTypesCategories::create([
			'category' => 'Безалкогольные напитки',
			'product_type_id' => $type->id,
			'slug' => Str::slug('Безалкогольные напитки')
		]);

    }
}
