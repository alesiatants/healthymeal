<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductTypesCategories extends Model
{

    use HasFactory;

	protected $fillable = [
			'category',
			'product_type_category_id',
			'slug',
	 ];

	/**
	 * 	Типы  продуктов
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function type(): BelongsTo
	{
			return $this->belongsTo(ProductType::class, "product_type_category_id");
	}
	/**
	 * Продукты по категории
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

        static::creating(function ($productTypeCategory) {
            $productTypeCategory->slug = Str::slug($productTypeCategory->type);
        });

        static::updating(function ($productTypeCategory) {
            $productTypeCategory->slug = Str::slug($productTypeCategory->category);
        });
    }
}
