<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\RecipeType;
use App\Models\Recipes;
use App\Models\Products;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Контроллер для работы с главной страницей: выгрузка каталога, фильтрация рецептов/продуктов
 */
class HomeController extends Controller
{
    /**
    * Отображение главной страницы с каталогом рецептов и продуктов
    *
    * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    */
    public function index()
    {
        Log::info("Главная страница, получение данных для каталога");
        $recipes = Recipes::selectRaw('id, slug, name, image, prep_time, calories, protein, fat, carbs, difficulty,created_at, description, "recipe" as item_type')
                       ->latest()
                       ->limit(12)
                       ->get();
        
        $products = Products::selectRaw('id, slug, name, image, calories, protein, fat, carbs, description, created_at, product_type_id, "product" as item_type, (SELECT slug FROM product_types WHERE product_types.id = products.product_type_id) as product_type_slug')
                        ->with('productType')
                         ->latest()
                         ->limit(12)
                         ->get();
        
        $items = $recipes->merge($products)->shuffle()->take(12);
        $recipeTypes = RecipeType::all();
        $productTypes = ProductType::with("categories")->get();
        return view('home.home', [
            'items' => $items,
            'recipeTypes' => $recipeTypes,
            'productTypes' => $productTypes
        ]);
    }
    
    /**
     * Фильтрация и поиск рецептов/продуктов
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response {
     *   "success": true,
     *   "html": "<div>...</div>",
     *   "pagination": "<ul class=\"pagination\">...</ul>"
     * }
     */
    public function filter(Request $request)
    {
        Log::info("Фильтрация рецептов/продуктов");
        $filters = $request->only(['search', 'type', 'sort', 'page']);
        Log::Debug("Данные запроса для фильтрации:\n" . json_encode($filters, JSON_UNESCAPED_UNICODE));

        $products = Products::filter($filters)
            ->with(['productType' => fn($q) => $q->select('id', 'slug')])
            ->select([
                'products.id',
                'products.name',
                'products.slug',
                'products.image',
                'products.created_at',
                'products.calories',
                'products.protein',
                'products.fat',
                'products.carbs',
                DB::raw('NULL as prep_time'),
                DB::raw('NULL as difficulty'),
                DB::raw('"product" as item_type'),
                DB::raw('product_types.slug as type_slug')
            ])
            ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id');

        $recipes = Recipes::filter($filters)
            ->with(['type' => fn($q) => $q->select('id', 'slug')])
            ->select([
                'recipes.id',
                'recipes.name',
                'recipes.slug',
                'recipes.image',
                'recipes.created_at',
                'recipes.calories',
                'recipes.protein',
                'recipes.fat',
                'recipes.carbs',
                'recipes.prep_time',
                'recipes.difficulty',
                DB::raw('"recipe" as item_type'),
                DB::raw('recipe_types.slug as type_slug') // То же самое имя
            ])
            ->leftJoin('recipe_types', 'recipes.recipe_type_id', '=', 'recipe_types.id');

        $query = match($filters['type'] ?? 'all') {
            'product' => $products,
            'recipe' => $recipes,
            default => $recipes->union($products)
        };
        $items = $query->orderBy('created_at', 'desc')
                    ->paginate(12);

        return response()->json([
            'success' => true,
            'html' => view('home.partials.items', ['items' => $items])->render(),
            'pagination' => $items->links()->toHtml()
        ]);
    }

}
