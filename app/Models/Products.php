<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\ProductTypesCategories;
use App\Models\ProductType;
use App\Models\RecipesProducts;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'image', 'description',
        'protein', 'fat', 'carbs', 'calories',
        'product_type_id', 'product_type_category_id'
    ];

    /**
     * Получение типа продукта
     * @return BelongsTo
     */
    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Получение категории продукта
     * @return BelongsTo
     */
    public function productTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ProductTypesCategories::class);
    }
    /**
     * Получение всех рецептов, в которых используется продукт
     * @return BelongsToMany
     */
    public function recipes(): BelongsToMany
    {
        return $this->BelongsToMany(Recipes::class, 'recipes_products')
        ->withPivot(['quantity', 'unit']);
    }
    /**
     * Фильтрация продуктов
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

    
    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }
}
