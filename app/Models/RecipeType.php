<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\Recipes;
use Illuminate\Support\Str;

class RecipeType extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'slug'];

    /**
     * Получить рецепты по типу
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipes::class, 'recipe_type_id');
    }

    /**
     * Получить все типы рецептов
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllTypes()
    {
        return self::all();
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recipeType) {
            $recipeType->slug = Str::slug($recipeType->type);
        });

        static::updating(function ($recipeType) {
            $recipeType->slug = Str::slug($recipeType->type);
        });
    }
}
