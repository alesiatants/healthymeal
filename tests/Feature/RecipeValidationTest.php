<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\ProductType;
use Illuminate\Support\Str;
use App\Models\Products;
use App\Models\RecipeType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecipeValidationTest extends TestCase
{
    use DatabaseTransactions;
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }
    #[Test]
    public function validates_non_granted_create_recipe()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'birth_date' => '1990-01-01',
            'phone' => '1234567890',
            'gender' => 'Мужской',]);
        $response = $this->actingAs($user)->postJson('/ownrecipes', []);
        $response->assertStatus(403);
    }
    #[Test]
    public function validates_no_data_create_recipe()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'birth_date' => '1990-01-01',
            'phone' => '1234567890',
            'gender' => 'Мужской',]);
        $dietologRole = Role::create(['name' => 'dietolog']);
        $user->assignRole('dietolog');
        $response = $this->actingAs($user)->postJson('/ownrecipes', [
            '_token' => '9Ic7Rd6M5rZ2rkdJx0woXQMvtvLSUpPCYpWGaz4m',
            'name' => 'ewgewg',
            'recipe_type_id' => '1',
            'prep_time' => NULL,
            'difficulty' => 'Легкий',
            'description' => NULL]);
        $response->assertStatus(400);
        $responseData = json_decode(json_decode($response->getContent(), true), true);
        $this->assertArrayHasKey('status', $responseData, true);
        $this->assertFalse($responseData['status']);
        $this->assertNotEmpty($responseData['validation']);
        $this->assertIsArray($responseData['validation']);
        $this->assertArrayHasKey('name', $responseData['validation']);
        $this->assertArrayHasKey('ingredients', $responseData['validation']);
        $this->assertEquals('Название рецепта должно состоять не менее чем из 10 символов.', $responseData['validation']['name']);}


    #[Test]
    public function validates_calculate_kbgd_recipe()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'birth_date' => '1990-01-01',
            'phone' => '1234567890',
            'gender' => 'Мужской',
        ]);
        $dietologRole = Role::create(['name' => 'dietolog']);
        $user->assignRole('dietolog');
        $productsType = ProductType::create([
            'type' => 'Мясо', 'slug' => Str::slug('Мясо')
        ]);
        $recipeType = RecipeType::create([
            'type' => 'Салаты', 'slug' => Str::slug('Салаты')
        ]);
        $product1 = Products::create([
           'name' => 'Мясо1',
                'slug' => Str::slug('Мясо1'),
                'image' => 'products/med.jpg',
                'description' => 'Ме́д — природный сладкий, вязкий продукт, ',
                'protein' => 8.4,
                'fat' => 0.7,
                'carbs' => 2.6,
                'calories' => 37,
                'product_type_id' => $productsType->id,
                'product_type_category_id' => null, 
        ]);
        $product2 = Products::create([
            'name' => 'Мясо2',
                'slug' => Str::slug('Мясо2'),
                'image' => 'products/med.jpg',
                'description' => 'Ме́д — природный сладкий',
                'protein' => 12.3,
                'fat' => 15,
                'carbs' => 4.8,
                'calories' => 52,
                'product_type_id' => $productsType->id,
                'product_type_category_id' => null,
        ]);
        $product3 = Products::create([
            'name' => 'Мясо3',
                'slug' => Str::slug('Мясо3'),
                'image' => 'products/med.jpg',
                'description' => 'Ме́д — природный сладкий, вязкий продукт, ',
                'protein' => 0.6,
                'fat' => 10,
                'carbs' => 16,
                'calories' => 73,
                'product_type_id' => $productsType->id,
                'product_type_category_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson('/ownrecipes', [
            '_token' => '9Ic7Rd6M5rZ2rkdJx0woXQMvtvLSUpPCYpWGaz4m',
            'name' => 'Тестовы рецепт',
            'recipe_type_id' => $recipeType->id,
            'prep_time' => '4',
            'difficulty' => 'Легкий',
            'description' => '4tr4t4tыВУЦКРИККККККпвупуЦПРПуПуПРМпппппппппппппппппппуПуПуППуППППППППППППППППППППППППППППППППППППППППППППППППППППППППППППП',
            'ingredients' => [
                [
                'product_id' => $product1->id,
                'quantity' => 4,
                'unit' => 'ст.л.',
                ],
                [
                'product_id' => $product2->id,
                'quantity' => 0.2,
                'unit' => 'кг',
                ],
                [
                    'product_id' => $product3->id,
                    'quantity' => 1,
                    'unit' => 'ч.л.',
                ]
            ],
            'steps' => [
                [ 'description' => 'edhrdehhrhuhewhfhueufuh eufghueghfhuehiuf eiuhfi8ehihfiheihf ejuhnfikjehifhioehif'],
                [ 'description' => 'edhrdehhrhuhewhfhueufuh eufghueghfhuehiuf eiuhfi8ehihfiheihf ejuhnfikjehifhioehif'],
                [ 'description' => 'edhrdehhrhuhewhfhueufuh eufghueghfhuehiuf eiuhfi8ehihfiheihf ejuhnfikjehifhioehif']
            ],
            'image' => UploadedFile::fake()->image('recipe.jpg')

        ]);
       // $response->assertRedirect(); // Проверяем успешное создание

        // Проверяем расчеты в базе данных
        $recipe = $user->ownRecipes()->first();
        
        // Итого:
        $expectedCalories = 129.85;
        $expectedProtein = 29.67;
        $expectedFat = 30.92;
        $expectedCarbs = 11.96;

        $this->assertEquals($expectedCalories, $recipe->calories);
        $this->assertEquals($expectedProtein, $recipe->protein);
        $this->assertEquals($expectedFat, $recipe->fat);
        $this->assertEquals($expectedCarbs, $recipe->carbs);
        $this->assertCount(3, $recipe->products);
    }
}

