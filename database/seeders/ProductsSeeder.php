<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Овощи -> Корнеплоды
          /*  [
                'name' => 'Морковь',
                'slug' => Str::slug('Морковь'),
                'image' => 'products/morkov.jpg',
                'description' => 'Свежая хрустящая морковь, богатая витамином А',
                'protein' => 0.9,
                'fat' => 0.2,
                'carbs' => 6.8,
                'calories' => 32,
                'product_type_id' => 7,
                'product_type_category_id' => null,
            ],
            [
                'name' => 'Свекла',
                'slug' => Str::slug('Свекла'),
                'image' => 'products/svekla.jpg',
                'description' => 'Красная свекла с насыщенным вкусом',
                'protein' => 1.5,
                'fat' => 0.1,
                'carbs' => 8.8,
                'calories' => 43,
                'product_type_id' => 7,
                'product_type_category_id' => null,
            ],
            
            // Овощи -> Листовые
            [
                'name' => 'Шпинат',
                'slug' => Str::slug('Шпинат'),
                'image' => 'products/shpinat.jpg',
                'description' => 'Листовой овощ с высоким содержанием железа',
                'protein' => 2.9,
                'fat' => 0.4,
                'carbs' => 1.4,
                'calories' => 23,
                'product_type_id' => 7,
                'product_type_category_id' => null,
            ],
            
            // Фрукты -> Цитрусовые
            [
                'name' => 'Апельсин',
                'slug' => Str::slug('Апельсин'),
                'image' => 'products/apelsiny.jpg',
                'description' => 'Сочные апельсины с высоким содержанием витамина C',
                'protein' => 0.9,
                'fat' => 0.2,
                'carbs' => 8.3,
                'calories' => 43,
                'product_type_id' => 8,
                'product_type_category_id' => 11,
            ],
            
            // Молочные продукты -> Сыры
            [
                'name' => 'Сыр Гауда',
                'slug' => Str::slug('Сыр Гауда'),
                'image' => 'products/syr-gauda.jpg',
                'description' => 'Полутвердый сыр с нежным ореховым вкусом',
                'protein' => 25.0,
                'fat' => 27.0,
                'carbs' => 2.2,
                'calories' => 356,
                'product_type_id' => 5,
                'product_type_category_id' => 8,
            ],
            
            // Мясо и птица -> Курица
            [
                'name' => 'Куриная грудка',
                'slug' => Str::slug('Куриная грудка'),
                'image' => 'products/kurinaya-grudka.jpg',
                'description' => 'Филе куриной грудки без кожи, диетический продукт',
                'protein' => 23.0,
                'fat' => 1.5,
                'carbs' => 0.0,
                'calories' => 110,
                'product_type_id' => 1,
                'product_type_category_id' => 3,
            ],
            
            // Бакалея -> Крупы
            [
                'name' => 'Гречневая крупа',
                'slug' => Str::slug('Гречневая крупа'),
                'image' => 'products/grechka.jpg',
                'description' => 'Ядрица гречневая, богата растительным белком',
                'protein' => 12.6,
                'fat' => 3.3,
                'carbs' => 62.1,
                'calories' => 313,
                'product_type_id' => 13,
                'product_type_category_id' => null,
            ],*/
           /* [
                'name' => 'Клюквенный морс',
                'slug' => Str::slug('Клюквенный морс'),
                'image' => 'products/klukvenniy-mors.jpg',
                'description' => 'Клю́квенный морс — напиток на основе ягод клюквы. Морс готовят из концентрированного клюквенного сока, смешивая с сахарным сиропом и водой.',
                'protein' => 0,
                'fat' => 0,
                'carbs' => 6.9,
                'calories' => 27.7,
                'product_type_id' => 14,
                'product_type_category_id' => 17,
            ],*/
            [
                'name' => 'Малина',
                'slug' => Str::slug('Малина'),
                'image' => 'products/malina.jpg',
                'description' => 'Малúна — розово-красный плод полукустарника из семейства Розовых. Ее обычно называют ягодой, но, согласно ботанической классификации, малина — многокостянка.',
                'protein' => 0.8,
                'fat' => 0.5,
                'carbs' => 8.3,
                'calories' => 46,
                'product_type_id' => 7,
                'product_type_category_id' => 11,
            ],
            [
                'name' => 'Банан',
                'slug' => Str::slug('Банан'),
                'image' => 'products/banan.jpg',
                'description' => 'Бана́н — плод многолетнего одноименного растения Банан (лат. Musa). По одной из версий, слово было заимствовано португальцами из языка баконго, народа, жившего на западе тропической Африки, где данное растение называлось baname, а русский вариант слова происходит уже от французского banane.',
                'protein' => 1.5,
                'fat' => 0.5,
                'carbs' => 21.0,
                'calories' => 96,
                'product_type_id' => 7,
                'product_type_category_id' => 11,
            ],
            [
                'name' => 'Йогурт натуральный 2,5 %',
                'slug' => Str::slug('Йогурт натуральный 2,5 %'),
                'image' => 'products/yuogurt-naturalniy-2-5.jpg',
                'description' => 'Йо́гурт натура́льный — не содержащий ГМО и искусственных добавок молочнокислый продукт, состоящий из цельного или восстановленного молока и йогуртовой закваски. Слово «йогурт» было заимствовано европейскими языками (yogourt франц. и yogurt англ.) из турецкого, а после из европейских языков попало в русский.',
                'protein' => 10.20,
                'fat' => 1,
                'carbs' => 3.6,
                'calories' => 59,
                'product_type_id' => 5,
                'product_type_category_id' => 9,
            ],
            [
                'name' => 'Мед',
                'slug' => Str::slug('Мед'),
                'image' => 'products/med.jpg',
                'description' => 'Ме́д — природный сладкий, вязкий продукт, который вырабатывают пчелы и некоторые виды насекомых после сбора нектара растений или медвяной росы. В пищу употребляется пчелиный цветочный мед.',
                'protein' => 0.30,
                'fat' => 0,
                'carbs' => 82.4,
                'calories' => 328,
                'product_type_id' => 13,
                'product_type_category_id' => null,
            ],


        ];

        DB::table('products')->insert($products);
    }
}