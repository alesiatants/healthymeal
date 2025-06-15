<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductTypesCategories;
use App\Models\Products;
use Illuminate\Support\Str;

class ProductType extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'slug'];
    
    /**
     * Категории типа продукта
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(ProductTypesCategories::class);
    }

    /**
     * Получить все типы продуктов с категориями
     * 
     * @param int|null $categorySlug
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredProductsByCategory ($categorySlug = null)
    {
        return $this->products()
        ->when($categorySlug, function($query, $categorySlug) {
            $query->whereHas('productTypeCategory', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        })->get();
    }

    /**
     * Получить все типы продуктов
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllTypes()
    {
        return self::all();
    }
    
    /**
     * Получить продукты по типу
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Products::class, 'product_type_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productType) {
            $productType->slug = Str::slug($productType->type);
        });

        static::updating(function ($productType) {
            $productType->slug = Str::slug($productType->type);
        });
    }
}
