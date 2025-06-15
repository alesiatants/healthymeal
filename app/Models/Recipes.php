<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\RecipeType;
use App\Models\RecipesSteps;
use App\Models\RecipesScore;
use App\Models\RecipesComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Recipes extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'image', 'description', 'protein', 'fat', 'carbs', 'calories', 'recipe_type_id', 'prep_time', 'difficulty', 'user_id', 'created_at', 'updated_at'];

    /**
     * Тип рецепта
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(RecipeType::class, 'recipe_type_id');
    }

    /**
     * Автор рецепта
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Шаги рецепта
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function steps(): HasMany
    {
        return $this->hasMany(RecipesSteps::class);
    }

    /**
     * Продукты рецепта
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'recipes_products')
        ->withPivot(['quantity', 'unit']);
    }

    /**
     * Фильтрация рецептов
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        });
        
        $query->when($filters['sort'] ?? 'newest', function($query, $sort) {
            
                $query->latest();
        });
        
        return $query;
    }

    /**
     * Получить рейтинг рецепта
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores(): HasMany
    {
        return $this->hasMany(RecipesScore::class);
    }

    /**
     * Получить комментарии к рецепту
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(RecipesComment::class)->latest();
    }

    /**
     * Получить пользователей, которые добавили рецепт в избранное
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
      return $this->belongsToMany(User::class, 'recipes_favorites');
    }

    /**
     * Получить пользователя, который создал рецепт
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ownUsers(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }
    /**
     * Проверка, добавлен ли рецепт в избранное пользователем
     * 
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function isFavoritedBy(?User $user): bool
    {
        if(!$user) {
            return false;
        }
        return $this->users()
        ->where('user_id', $user->id)
        ->exists();
    }
   
    /**
     * Получить все рецепты с пагинацией
     * 
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecipeData($id)
    {
        $query = self::query()
        ->withCount(['scores', 'comments'])
        ->with(['products', 'author', 'type'])
        ->findOrFail($id);
        return $query;
    }

    /**
     * Получить собственные рецепты с пагинацией
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOwnRecipes(User $user)
    {
        return $user->ownRecipes()
               ->with(['type', 'author', 'products'])
               ->withCount(['scores', 'comments'])
               ->latest()
               ->paginate(10);
    }

    /**
     * Получить избранные рецепты с пагинацией
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFavoritesRecipes(User $user)
    {
        return $user->recipes()
               ->with(['type', 'author', 'products'])
               ->withCount(['scores', 'comments'])
               ->latest()
               ->paginate(10);
    }
    
    /**
     * Получить рецепты по категории с пагинацией
     * 
     * @param \App\Models\RecipeType|null $recipeType
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFilteredRecipesByCategory($recipeType = null, $request)
    {
        $query = $recipeType ? $recipeType->recipes() : self::query();
        $query->with(['type', 'author', 'scores'])
        ->withCount('scores')
        ->latest();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');}
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->input('difficulty'));}
        if ($request->filled('max_time')) {
            $query->where('prep_time', '<=', $request->input('max_time')); }
        if ($request->filled('include_ingredients')) {
            $includeIngredientsInput = $request->input('include_ingredients');
            if (is_string($includeIngredientsInput)) {
                $includeIngredientIds = explode(',', $includeIngredientsInput);
                $includeIngredientIds = array_filter(array_map('intval', $includeIngredientIds), function($id) {
                    return $id > 0;
                });
                if (!empty($includeIngredientIds)) {
                    $query->whereHas('products', function ($q) use ($includeIngredientIds) {
                        $q->whereIn('products.id', $includeIngredientIds);
                    }, '=', count($includeIngredientIds));}
            } else {
                Log::warning('Unexpected input for include_ingredients:', ['input' => $includeIngredientsInput, 'type' => gettype($includeIngredientsInput)]);
            }}
        if ($request->filled('exclude_ingredients')) {
            $excludeIngredientsInput = $request->input('exclude_ingredients');
            if (is_string($excludeIngredientsInput)) {
                $excludeIngredientIds = explode(',', $excludeIngredientsInput);
                $excludeIngredientIds = array_filter(array_map('intval', $excludeIngredientIds), function($id) {
                    return $id > 0; 
                });
                if (!empty($excludeIngredientIds)) {
                    $query->whereDoesntHave('products', function ($q) use ($excludeIngredientIds) {
                        $q->whereIn('products.id', $excludeIngredientIds);
                    }); }} else {
                Log::warning('Unexpected input for exclude_ingredients:', ['input' => $excludeIngredientsInput, 'type' => gettype($excludeIngredientsInput)]);} }
        return $query->paginate(9);
    }

    /**
     * Применить фильтры к запросу
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected static function applyFilters($query, $filters)
    {
        //Фильтрация по времени приголтовления
        if (isset($filters['max_time'])) {
            $query->where('prep_time', '<=', $filters['max_time']);
        }
        //Фильтрация по сложности
        if (isset($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }
        //Поиск по названию
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' .  $filters['search'] . '%');
        }
    }

    /**
     * Получить рецепты по типу приема пищи - завтрак
     * 
     * @param float $calories
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function typeZavtrakFilter ($calories)
    {
        $query = self::query();
        $excludedCategories = ['Консервы', 'Соусы', 'Гарниры', 'Закуски'];
        $excludedProductTypes = ['Мясо', "Рыба", "Морепродукты", "Соевые продукты", "Грибы"];
        return $query->where('calories', "<", $calories)
                     ->whereDoesntHave('type', function($query) use ($excludedCategories) {
                        $query->whereIn('type', $excludedCategories);
                    })
                    ->whereDoesntHave('products.productType', function($query) use ($excludedProductTypes) {
                        $query->whereIn('type', $excludedProductTypes);
                    })
                    ->with('type')
                    ->inRandomOrder()
                    ->get()
                    ->groupBy('recipe_type_id')
                    ->flatMap(function($group) {
                        return $group->take(2);
                    });
    }

     /**
     * Получить рецепты по типу приема пищи - обед
     * 
     * @param float $calories
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function typeObedFilter ($calories)
    {
        $query = self::query();
        $excludedCategories = ['Консервы', 'Выпечка', 'Десерты', 'Гарниры', 'Закуски'];
        $excludedProductTypes = ['Мучные продукты', "Соевые продукты", "Орехи"];
        return $query->where('calories', "<", $calories)
                     ->whereDoesntHave('type', function($query) use ($excludedCategories) {
                        $query->whereIn('type', $excludedCategories);
                    })
                    ->whereDoesntHave('products.productType', function($query) use ($excludedProductTypes) {
                        $query->whereIn('type', $excludedProductTypes);
                    })
                    ->with('type')
                    ->inRandomOrder()
                    ->get()
                    ->groupBy('recipe_type_id')
                    ->flatMap(function($group) {
                        return $group->take(2);
                    });
    }

     /**
     * Получить рецепты по типу приема пищи - перекус
     * 
     * @param float $calories
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function typePerekusFilter ($calories)
    {
        $query = self::query();
        $excludedCategories = ['Консервы', 'Гарниры', 'Первые блюда'];
        $excludedProductTypes = ["Соевые продукты", "Грибы", "Морепродукты", "Мясо"];
        return $query->where('calories', "<", $calories)
                     ->whereDoesntHave('type', function($query) use ($excludedCategories) {
                        $query->whereIn('type', $excludedCategories);
                    })
                    ->whereDoesntHave('products.productType', function($query) use ($excludedProductTypes) {
                        $query->whereIn('type', $excludedProductTypes);
                    })
                    ->with('type')
                    ->inRandomOrder()
                    ->get()
                    ->groupBy('recipe_type_id')
                    ->flatMap(function($group) {
                        return $group->take(2);
                    });
    }

     /**
     * Получить рецепты по типу приема пищи - ужин
     * 
     * @param float $calories
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function typeUginFilter ($calories)
    {
        $query = self::query();
        $excludedCategories = ['Выпечка', 'Закуски'];
        $excludedProductTypes = ['Мучные продукты', ];
        return $query->where('calories', "<", $calories)
                     ->whereDoesntHave('type', function($query) use ($excludedCategories) {
                        $query->whereIn('type', $excludedCategories);
                    })
                    ->whereDoesntHave('products.productType', function($query) use ($excludedProductTypes) {
                        $query->whereIn('type', $excludedProductTypes);
                    })
                    ->with('type')
                    ->inRandomOrder()
                    ->get()
                    ->groupBy('recipe_type_id')
                    ->flatMap(function($group) {
                        return $group->take(2);
                    });
    }

    /**
     * Получит данные о рейтинге рецепта
     * 
     * @return array
     */
    public function getRatingData() :array
    {
        return [
            'avg'   => round($this->rating_avg ?? 0, 1),
            'stars' => min(5, max(0, round(($this->rating_avg ?? 0) * 2) / 2)),
            'count' => $this->rating_count ?? 0
        ];
    }

    /**
     * Получить все сложности рецепта
     * 
     * @return array
     */
    public static function getDifficulties(): array
    {
        return [
            'Легкий'   => 'Легкий',
            'Средний' => 'Средний',
            'Сложный'   => 'Сложный'
        ];
    }

    /**
     * Получить средний рейтинг рецепта
     * 
     * @return float
     */
    public function averageRating(): float
    {
        return $this->scores()->avg('score') ?? 0;
    }

    /**
     * Получить оценку рецепта
     * 
     * @return int
     */
    public function getStarRating()
    {
        $avg = $this->averageRating();
        return min(5, max(0, round($avg * 2) / 2));
    }
    
    public static function boot()
    {
        parent::boot();

        static::creating(function ($recipe) {
            $recipe->slug = Str::slug($recipe->name);
        });

        static::updating(function ($recipe) {
            $recipe->slug = Str::slug($recipe->name);
        });
    }
}
