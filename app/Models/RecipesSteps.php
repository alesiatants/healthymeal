<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Recipes;

class RecipesSteps extends Model
{
    use HasFactory;
    protected $fillable = ['recipe_id', 'step_number', 'description', 'updated_)at', 'created_at'];

    /**
     * Получить рецепт, к которому относится шаг
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipes::class);
    }
}
