<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipes;
use App\Models\Products;
use App\Models\RecipeType;
use App\Models\RecipesSteps;
use App\Models\RecipesScore;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Services\ProductPriceService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\DTO\ErrorResponse;
use Exception;
use Illuminate\Support\Facades\View;
use Censure\Censure;
/**
 * Этот контроллер отвечает за управление рецептами.
 */
class RecipesController extends Controller
{
    /**
     * Путь к изображениям рецептов.
     * @var string
     */
    const IMAGE_PATH = "/var/www/healthymeal/healthymeal/public/storage/recipes/";

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * Конструктор контроллера.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    
    /**
     * Отображает список рецептов по типу.
     * 
     * @param RecipeType $recipeType
     * @param Request $request
     * @return \Illuminate\View\View
     */
     public function index(RecipeType $recipeType, Request $request)
     {
        Log::info("Фильтр по типу рецепта: " . $recipeType->type);
        $recipes = Recipes::getFilteredRecipesByCategory($recipeType, $request);

        $difficulties = Recipes::getDifficulties();

        if ($request->ajax()) {
            $recipeListHtml = View::make('recipes.list', compact('recipes'))
                                ->render(); 
            $pagination_html = $recipes->links()->render();

            return response()->json([
                'recipe_list_html' => $recipeListHtml,
                'pagination_html' => $pagination_html,
            ]);
        }

        return view('recipes.index', compact('recipeType', 'recipes', 'difficulties'));
    }


    /**
     * Поиск ингредиентов по имени.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function searchIngredients(Request $request)
    {
        $query = $request->input('query');
        $ingredients = Ingredient::where('name', 'like', '%' . $query . '%')
                                ->limit(10)
                                ->get(['id', 'name']);

        return response()->json($ingredients);
    }

     /**
      * Рассчитывает стоимость рецепта на основе ингредиентов.
      *
      * @param Request $request
      * @param ProductPriceService $service
      * @return \Illuminate\Http\JsonResponse
      * @throws \Illuminate\Validation\ValidationException
      * @throws \Exception
      * @throws \JMS\Serializer\Exception\RuntimeException
      */
     public function calculateCost(Request $request, ProductPriceService $service)
     {
        Log::info("Рассчет стоимости рецепта");
         $request->validate([
             'ingredients' => 'required|array',
             'ingredients.*.id' => 'required|integer|exists:products,id',
             'ingredients.*.quantity' => 'required|numeric|min:0', ]);
         $totalCost = 0;
         $calculatedItems = [];
         foreach ($request->ingredients as $ingredient) {
             $product = Products::find($ingredient['id']);    
             if (!$product) {
                 continue; }
             $response = $service->searchProducts($product->name);
             if ($response->code !== 200) {
                return response()->json([
                    'success' => false,
                    'message' => $response->message ]);}
                $quantity = $ingredient['quantity'] ? ($service->calculateGramm($ingredient['quantity'], $ingredient['unit'])/1000) : 1.0;
                $avgPrice = $service->calculateAveragePrice(
                    $response->items,
                    $product->name,
                    $product->productType->type,
                    $quantity,
                );
                $proposition = $service->filterProposition($response->items,  $product->name, $product->productType->type);
                $avgPricePerKg = $service->calculateAveragePricePerKG($response->items,  $product->name, $product->productType->type);
                $avgPricePerUnit = $service->calculateAveragePricePerUnit($response->items,  $product->name, $product->productType->type);
                $calculatedItems[] = [
                    'name' => $product->name,
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                    'price_per_unit' =>  $avgPricePerUnit,
                    'cost' =>  round($avgPrice, 2),
                    'products' => $proposition ];
                $totalCost +=  $avgPrice;  }
         return response()->json([
             'success' => true,
             'costs' => $calculatedItems,
             'total_cost' => round($totalCost, 2),
         ]);
     }
 
     /**
      * Отображает страницу с рецептом.
      *
      * @param RecipeType $recipeType
      * @param Recipes $recipe
      * @return \Illuminate\View\View
      */
    public function show(RecipeType $recipeType, Recipes $recipe)
    {
        Log::info("Показать рецепт: " . $recipe->name);
        $recipe = Recipes::getRecipeData($recipe->id);
        return view('recipes.show', compact('recipe', 'recipeType'));
    }

    /**
     * Добавляет оценку рецепта.
     * 
     * @param Request $request
     * @param string $recipeType
     * @param Recipes $recipe
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function storeRating(Request $request, $recipeType, Recipes $recipe)
    {
        try{
            Log::info("Добавление оценки");
            $validated = $request->validate([
                'rating' => 'required|integer|between:1,5'
            ]);
            DB::beginTransaction();

            $rating = new RecipesScore([
                'score' => $validated['rating'],
                'user_id' => Auth::id(),
                'recipes_id' => $recipe->id
            ]);

            $rating->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'average_rating' => $recipe->fresh()->averageRating(),
                'ratings_count' => $recipe->fresh()->scores()->count()
            ]);
         }
        catch (Exception $e) {
            Log::error('Ошибка при оценке рецепта: ' . $e->getMessage());
            DB::rollBack();
            $error = new ErrorResponse(false, [], 'Ошибка при оценке рецепта: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
    }

     /**
     * Обновляем оценку рецепта.
     * 
     * @param Request $request
     * @param string $recipeType
     * @param Recipes $recipe
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function updateRating(Request $request, $recipeType, Recipes $recipe)
    {
        try{
            Log::info("Обновление оценки");
            $validated = $request->validate([
                'rating' => 'required|integer|between:1,5'
            ]);
            DB::beginTransaction();
            $rating = RecipesScore::where('user_id', Auth::id())
                ->where('recipes_id', $recipe->id)
                ->firstOrFail();

            $rating->update(['score' => $validated['rating']]);
            DB::commit();
            return response()->json([
                'success' => true,
                'average_rating' => $recipe->fresh()->averageRating(),
                'ratings_count' => $recipe->fresh()->scores()->count()
            ]);
        }
        catch (Exception $e) {
            Log::error('Ошибка при оценке рецепта: ' . $e->getMessage());
            DB::rollBack();
            $error = new ErrorResponse(false, [], 'Ошибка при оценке рецепта: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            Log::error($response);
            return $response;
        }
    }
 
    /**
     * Отображает форму создания нового рецепта.
     * 
     * @return \Illuminate\View\View
     */
     public function create()
     { 
        Log::info("Форма создания нового рецепта");
         return view('recipes.store', [
             'recipeTypes' => RecipeType::all(),
             'products' => Products::all(),
             'difficulties' => [
                 'easy' => 'Легкий',
                 'medium' => 'Средний',
                 'hard' => 'Сложный'
             ]
         ]);
     }

     /**
      * Отображает страницу с рецептами диетолога.
      *
      * @return \Illuminate\View\View
      */
     public function showOwnRecipes()
     {
        Log::info("Показать свои рецепты");
        $recipes = Recipes::getOwnRecipes(Auth::user());
        return view('user.ownrecipesshow', compact('recipes'));
     }

     /**
      * Отображает страницу с избранными рецептами.
      *
      * @return \Illuminate\View\View
      */
     public function showFavoritesRecipes()
     {
        Log::info("Показать избранные рецепты");
        $recipes = Recipes::getFavoritesRecipes(Auth::user());
        return view('user.favoritesrecipesshow', compact('recipes'));
     }
 
     /**
      * Сохраняет новый рецепт.
      *
      * @param Request $request
      * @return \Illuminate\Http\RedirectResponse
      * @throws \Illuminate\Validation\ValidationException
      * @throws \Exception
      */
     public function store(Request $request)
     { 
        $lexicon = [];
        try {
            Log::info("Создание нового рецепта");
            Log::debug($request);
            $validated = $this->validateRecipe($request);
            $testLexicon = Censure::false_if_no_bad_words($validated['name']);
            if ($testLexicon === true) {
                $lexicon["name"] = "Обнаружена нецензурная лексика";}
            $testLexicon = Censure::false_if_no_bad_words($validated['description']);
            if ($testLexicon === true) {
                $lexicon["description"] = "Обнаружена нецензурная лексика";}
            foreach ($validated['steps'] as $index => $step) {
                $testLexicon = Censure::false_if_no_bad_words($step['description']);
                if ($testLexicon === true) {
                    $lexicon["steps[" . $index . "][description]"] = "Обнаружена нецензурная лексика";
                } 
            }
            if (count($lexicon) > 0) {
                $error = new ErrorResponse(false, $lexicon, "");
                $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
                return $response;
            }
            if ($testLexicon === true) {
                $error = new ErrorResponse(false, ["name" => "Обнаружена нецензурная лексика"], "");
                $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
                return $response;
            }
            DB::beginTransaction();
            $image = $request->file('image');
            $slug = Str::slug($validated['name']);
            $imageName = $slug . ".jpg";
            $path = $image->move(self::IMAGE_PATH, $imageName);
            $totalCalories = 0;
            $totalProtein = 0;
            $totalFat = 0;
            $totalCarbs = 0;
            $ingredientsData = [];
            foreach ($validated['ingredients'] as $ingredient) {
                $product = Products::findOrFail($ingredient['product_id']);
                $grams = $this->convertToGrams((float)$ingredient['quantity'],  $ingredient['unit']);
                $totalCalories += ($product->calories /100) * $grams;
                $totalProtein += ($product->protein /100) * $grams;
                $totalFat += ($product->fat /100) * $grams;
                $totalCarbs += ($product->carbs /100) * $grams;
                $ingredientsData[$ingredient['product_id']] = [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                    'grams' => $grams];
            }
            $recipe = Recipes::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'],
                'prep_time' => $validated['prep_time'],
                'difficulty' => $validated['difficulty'],
                'calories' => $totalCalories,
                'protein' => $totalProtein,
                'fat' => $totalFat,
                'carbs' => $totalCarbs,
                'image' => 'recipes/' . $imageName,
                'recipe_type_id' => $validated['recipe_type_id'],
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $recipe->products()->sync($ingredientsData);
            $steps = [];
            foreach ($validated['steps'] as $index => $step) {
                $steps[] = new RecipesSteps([
                    'description' => $step['description'],
                    'step_number' => $index+1
                ]);
            }
            $recipe->steps()->saveMany($steps);
            DB::commit();
            return redirect()->route('recipes.show', [
                'recipeType' => $recipe->type->slug,
                'recipe' => $recipe->slug
            ])->with('success', 'Рецепт успешно создан!');
        } catch (ValidationException $e) {
            Log::error('Ошибка при создании рецепта: ' . $e->getMessage());
            $flatErrors = [];
            foreach ($e->errors() as $field => $messages) {
               $newField = str_replace('.', '[',$field);
               $newField = preg_replace('/\[(\w+)$/', '][$1]', $newField);
               $flatErrors[$newField] = $messages[0];
            }    
            Log::debug($flatErrors);   
            if ($request->wantsJson() || $request->ajax()) {
                $error = new ErrorResponse(false, $flatErrors, "");
                 $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
                return $response;
            }
            return back()
                ->withErrors($flatErrors)
                ->withInput();  
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при создании рецепта: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при создании рецепта: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
        
     }
     /**
      * Конвертирует количество ингредиента в граммы.
      * 
      * @param float $quantity
      * @param string $unit
      * @return float
      * @throws \Exception
      */
     private function convertToGrams(float $quantity, string $unit): float
    {
        switch ($unit) {
            case 'г': 
                return $quantity;
                break;
            case 'кг': 
                return $quantity * 1000;
                break;
            case 'мл': 
                return $quantity; // предполагаем плотность ~1 г/мл для жидкостей
                break;
            case 'л': 
                return $quantity * 1000;
                break;
            case 'ч.л.': 
                return $quantity * 5; // чайная ложка ~5г
                break;
            case 'ст.л.': 
                return $quantity * 15; // столовая ложка ~15г
                break;
            case 'ст': 
                return $quantity * 240; // стакан ~240г
                break;
            case 'шт': 
                return $quantity * 100; // условно 1 шт = 100г
                break;
            default: 
                return $quantity; // если единица неизвестна, считаем как граммы
                break;
        }
    }
 
     /**
      * Отображает форму редактирования рецепта.
      *
      * @param Recipes $recipe
      * @return \Illuminate\View\View
      */
     public function edit(Recipes $recipe)
     {
        Log::info('Редактирование рецепта: ' . $recipe->name);
        $recipe = Recipes::getRecipeData($recipe->id);
 
         return view('recipes.store', [
             'recipe' => $recipe,
             'recipeTypes' => RecipeType::all(),
             'products' => Products::all(),
             'difficulties' => [
                 'easy' => 'Легкий',
                 'medium' => 'Средний',
                 'hard' => 'Сложный'
             ]
         ]);
     }
 
     
    /**
        * Обновляет рецепт.
        *
        * @param Request $request
        * @param Recipes $recipe
        * @return \Illuminate\Http\RedirectResponse
        * @throws \Illuminate\Validation\ValidationException
        * @throws \Exception
    */
     public function update(Request $request, Recipes $recipe)
     { 
        try {
            Log::info("Обновление рецепта: " . $recipe->name);
            $validated = $this->validateRecipeUpdate($request);
    
            DB::beginTransaction();
             
             // Генерируем новый slug, если изменилось название
            if ($recipe->name !== $validated['name']) {
                 $recipe->slug = Str::slug($validated['name']);
            }
              // Обновляем изображение если загружено новое
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $recipe->slug . ".jpg";
                $path = $image->move(self::IMAGE_PATH, $imageName);
                 $recipe->image = 'recipes/' . $imageName;
            }
            $totalCalories = 0;
            $totalProtein = 0;
            $totalFat = 0;
            $totalCarbs = 0;
             
             // Обновляем данные рецепта
             $recipe->update([
                 'name' => $validated['name'],
                 'description' => $validated['description'],
                 'prep_time' => $validated['prep_time'],
                 'difficulty' => $validated['difficulty'],
                 'calories' => $totalCalories,
                 'protein' => $totalProtein,
                 'fat' => $totalFat,
                 'carbs' => $totalCarbs,
                 'recipe_type_id' => $validated['recipe_type_id'],
                 'updated_at' => now(),
             ]);
             
             // Обновляем ингредиенты
             $ingredientsData = [];
             foreach ($validated['ingredients'] as $ingredient) {
                $product = Products::findOrFail($ingredient['product_id']);
                $grams = $this->convertToGrams($ingredient['quantity'],  $ingredient['unit']);
                $totalCalories += ($product->calories /100) * $grams;
                $totalProtein += ($product->protein /100) * $grams;
                $totalFat += ($product->fat /100) * $grams;
                $totalCarbs += ($product->carbs /100) * $grams;
                $ingredientsData[$ingredient['product_id']] = [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                    'grams' => $grams
                ];
             }
              // Обновляем данные рецепта
              $recipe->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'prep_time' => $validated['prep_time'],
                'difficulty' => $validated['difficulty'],
                'calories' => $totalCalories,
                'protein' => $totalProtein,
                'fat' => $totalFat,
                'carbs' => $totalCarbs,
                'recipe_type_id' => $validated['recipe_type_id'],
                'updated_at' => now(),
            ]);
             $recipe->products()->sync($ingredientsData);
             
             // Обновляем шаги приготовления
             $existingStepIds = [];
             $stepNumber = 1;
             foreach ($validated['steps'] as $step) {
                $stepData = [
                    'description' => $step['description'],
                    'step_number' => $stepNumber++,
                ];
                 if (isset($step['id'])) {
                     // Обновляем существующий шаг
                     RecipesSteps::where('id', $step['id'])
                         ->update($stepData);
                     $existingStepIds[] = $step['id'];
                 } else {
                     // Добавляем новый шаг
                     $newStep = $recipe->steps()->create($stepData);
                     $existingStepIds[] = $newStep->id;
                 }
             }
             // Удаляем шаги, которых нет в обновленных данных
             $recipe->steps()->whereNotIn('id', $existingStepIds)->delete();
             
             DB::commit();
             
             return redirect()->route('recipes.show', [
                'recipeType' => $recipe->type->slug,
                'recipe' => $recipe->slug
             ])->with('success', 'Рецепт успешно обновлен!');
             
         }  catch (ValidationException $e) {
            Log::error('Ошибка при обновлении рецепта: ' . $e->getMessage());
            $flatErrors = [];
            foreach ($e->errors() as $field => $messages) {
               $newField = str_replace('.', '[',$field);
               $newField = preg_replace('/\[(\w+)$/', '][$1]', $newField);
               $flatErrors[$newField] = $messages[0];
            }     
            if ($request->wantsJson() || $request->ajax()) {
                $error = new ErrorResponse(false, $flatErrors, "");
                 $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
                return $response;
            }
            return back()
                ->withErrors($flatErrors)
                ->withInput();  
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при обновлении рецепта: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при обновлении рецепта: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
     }
 
    /**
        * Удаляет рецепт.
        *
        * @param Recipes $recipe
        * @return \Illuminate\Http\RedirectResponse
        * @throws \Exception
    */
     public function destroy(Recipes $recipe)
     { 
        try {
            Log::info("Удаление рецепта: " . $recipe->name);
            DB::beginTransaction();
            // Удаляем связанные данные
            $recipe->products()->detach();
            $recipe->steps()->delete();
            $recipe->comments()->delete();
            $recipe->users()->delete();
            $recipe->scores()->delete();
            // Удаляем изображение
            $imageName = $recipe->slug . ".jpg";
            if (preg_match('/[^\/]+$/', $recipe->image, $matches)) {
                $oldImageName = $matches[0];
                unlink(self::IMAGE_PATH . $oldImageName);
            }
            
            // Удаляем сам рецепт
            $recipe->delete();
            
            DB::commit();
            $recipes = Recipes::getOwnRecipes(Auth::user());
            return redirect()->route('recipes.showown', compact('recipes'))
                   ->with('success', 'Рецепт успешно удален!');
                   
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при удалении рецепта: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при удалении рецепта: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
     }
 
    /**
        * Добавляет комментарий к рецепту.
        *
        * @param Request $request
        * @param Recipes $recipe
        * @return \Illuminate\Http\RedirectResponse
        * @throws \Illuminate\Validation\ValidationException
        * @throws \Exception
    */
     public function addCComment(Request $request, Recipe $recipe)
     {
        Log::info("Добавление комментария к рецепту: " . $recipe->name);
         $request->validate([
             'content' => 'required|string|max:1000'
         ]);
 
         $recipe->comments()->create([
             'user_id' => Auth::id(),
             'comment' => $request->content
         ]);
 
         return back()->with('success', 'Комментарий добавлен!');
     }
 
    /**
        * Валидирует данные рецепта при создании.
        *
        * @param Request $request
        * @return array
        * @throws \Illuminate\Validation\ValidationException
    */
     protected function validateRecipe(Request $request): array
     {
        $rules = [
            'name' => 'required|string|max:255|min:10',
            'recipe_type_id' => 'required|exists:recipe_types,id',
            'image' => 'required|image|max:2048',
            'description' => 'required|string',
            'prep_time' => 'required|integer|min:1',
            'difficulty' => 'required|in:Легкий,Средний,Сложный',
           'ingredients' => [
                'required',
                'array',
                'min:3',
                function ($attribute, $value, $fail) {
                    $productIds = array_column($value, 'product_id');
                    if (count($productIds) !== count(array_unique($productIds))) {
                        $fail('Ингредиенты не должны повторяться.');
                    }
                }
            ],
            'ingredients.*.product_id' => 'required|exists:products,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.1',
            'ingredients.*.unit' => 'required|string|max:3000',
            'steps' => [
                'required',
                'array',
                'min:3'
            ],
            'steps.*.description' => 'required|string|min:10|max:1000',
         ];
         $messages = [
            // Общие сообщения
            'required' => 'Поле :attribute обязательно для заполнения.',
            'min' => [
                'string' => 'Поле :attribute должно содержать не менее :min символов.',
                'numeric' => 'Поле :attribute должно быть не меньше :min.',
                'array' => 'Необходимо выбрать хотя бы :min элементов в :attribute.'
            ],
            'max' => [
                'string' => 'Поле :attribute не должно превышать :max символов.',
                'file' => 'Файл не должен быть больше :max килобайт.'
            ],
            'exists' => 'Выбранный :attribute не существует.',
            
            // Конкретные поля
            'name.required' => 'Название рецепта обязательно для заполнения.',
            'name.min' => 'Название рецепта должно состоять не менее чем из 10 символов.',
            'name.max' => 'Название рецепта не должно превышать 255 символов.',
            
            'recipe_type_id.required' => 'Необходимо выбрать тип рецепта.',
            
            'image.image' => 'Файл должен быть изображением.',
            'image.max' => 'Размер изображения не должен превышать 2MB.',
            
            'description.required' => 'Описание рецепта обязательно для заполнения.',
            'description.min' => 'Описание должно содержать не менее 20 символов.',
            'description.max' => 'Описание не должно превышать 500 символов.',
            
            'prep_time.required' => 'Укажите время приготовления.',
            'prep_time.integer' => 'Время приготовления должно быть целым числом.',
            'prep_time.min' => 'Время приготовления должно быть не менее 2 минут.',
            
            'difficulty.required' => 'Укажите сложность приготовления.',
            'difficulty.in' => 'Выберите корректный уровень сложности.',
            
            'ingredients.required' => 'Добавьте хотя бы 3 ингредиента.',
            'ingredients.min' => 'Добавьте не менее 3 ингредиентов.',
            
            'ingredients.*.product_id.required' => 'Для каждого ингредиента выберите продукт.',
            'ingredients.*.product_id.exists' => 'Выбранный продукт не существует.',
            
            'ingredients.*.quantity.required' => 'Укажите количество для каждого ингредиента.',
            'ingredients.*.quantity.numeric' => 'Количество должно быть числом.',
            'ingredients.*.quantity.min' => 'Количество должно быть не менее 0.1.',
            
            'ingredients.*.unit.required' => 'Укажите единицы измерения для ингредиента.',
            'ingredients.*.unit.max' => 'Название единицы измерения не должно превышать 20 символов.',
            
            'steps.required' => 'Добавьте хотя бы 3 шага приготовления.',
        'steps.min' => 'Добавьте не менее 3 шагов приготовления.',
            
            'steps.*.description.required' => 'Каждый шаг должен содержать описание.',
            'steps.*.description.min' => 'Описание шага должно содержать не менее 10 символов.',
            'steps.*.description.max' => 'Описание шага не должно превышать 255 символов.',
            
            'steps.*.id.exists' => 'Выбранный шаг не существует.'
        ];
        
        // Валидация данных
        return $request->validate($rules, $messages);
     }

     /**
        * Валидирует данные рецепта при обновлении.
        *
        * @param Request $request
        * @return array
        * @throws \Illuminate\Validation\ValidationException
    */
     protected function validateRecipeUpdate(Request $request): array
     {
        $rules = [
            'name' => 'required|string|max:255|min:10',
            'recipe_type_id' => 'required|exists:recipe_types,id',
            'image' => 'nullable|image|max:2048',
            'description' => 'required|string|max:1000|min:20',
            'prep_time' => 'required|integer|min:2',
            'difficulty' => 'required|in:Легкий,Средний,Сложный',
            'ingredients' => [
                'required',
                'array',
                'min:3',
                function ($attribute, $value, $fail) {
                    $productIds = array_column($value, 'product_id');
                    if (count($productIds) !== count(array_unique($productIds))) {
                        $fail('Ингредиенты не должны повторяться.');
                    }
                }
            ],
            'ingredients.*.product_id' => 'required|exists:products,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.1',
            'ingredients.*.unit' => 'required|string|max:20',
            'steps' => [
                'required',
                'array',
                'min:3'
            ],
            'steps.*.description' => 'required|string|min:10|max:1000',
            'steps.*.id' => 'sometimes|exists:recipe_steps,id',
        ];
        
        $messages = [
            // Общие сообщения
            'required' => 'Поле :attribute обязательно для заполнения.',
            'min' => [
                'string' => 'Поле :attribute должно содержать не менее :min символов.',
                'numeric' => 'Поле :attribute должно быть не меньше :min.',
                'array' => 'Необходимо выбрать хотя бы :min элементов в :attribute.'
            ],
            'max' => [
                'string' => 'Поле :attribute не должно превышать :max символов.',
                'file' => 'Файл не должен быть больше :max килобайт.'
            ],
            'exists' => 'Выбранный :attribute не существует.',
            
            // Конкретные поля
            'name.required' => 'Название рецепта обязательно для заполнения.',
            'name.min' => 'Название рецепта должно состоять не менее чем из 10 символов.',
            'name.max' => 'Название рецепта не должно превышать 255 символов.',
            
            'recipe_type_id.required' => 'Необходимо выбрать тип рецепта.',
            
            'image.image' => 'Файл должен быть изображением.',
            'image.max' => 'Размер изображения не должен превышать 2MB.',
            
            'description.required' => 'Описание рецепта обязательно для заполнения.',
            'description.min' => 'Описание должно содержать не менее 20 символов.',
            'description.max' => 'Описание не должно превышать 500 символов.',
            
            'prep_time.required' => 'Укажите время приготовления.',
            'prep_time.integer' => 'Время приготовления должно быть целым числом.',
            'prep_time.min' => 'Время приготовления должно быть не менее 2 минут.',
            
            'difficulty.required' => 'Укажите сложность приготовления.',
            'difficulty.in' => 'Выберите корректный уровень сложности.',
            
            'ingredients.required' => 'Добавьте хотя бы 3 ингредиента.',
            'ingredients.min' => 'Добавьте не менее 3 ингредиентов.',
            
            'ingredients.*.product_id.required' => 'Для каждого ингредиента выберите продукт.',
            'ingredients.*.product_id.exists' => 'Выбранный продукт не существует.',
            
            'ingredients.*.quantity.required' => 'Укажите количество для каждого ингредиента.',
            'ingredients.*.quantity.numeric' => 'Количество должно быть числом.',
            'ingredients.*.quantity.min' => 'Количество должно быть не менее 0.1.',
            
            'ingredients.*.unit.required' => 'Укажите единицы измерения для ингредиента.',
            'ingredients.*.unit.max' => 'Название единицы измерения не должно превышать 20 символов.',
            
            'steps.required' => 'Добавьте хотя бы 3 шага приготовления.',
        'steps.min' => 'Добавьте не менее 3 шагов приготовления.',
            
            'steps.*.description.required' => 'Каждый шаг должен содержать описание.',
            'steps.*.description.min' => 'Описание шага должно содержать не менее 10 символов.',
            'steps.*.description.max' => 'Описание шага не должно превышать 255 символов.',
            
            'steps.*.id.exists' => 'Выбранный шаг не существует.'
        ];
        
        // Валидация данных
        return $request->validate($rules, $messages);
    
     }
}
