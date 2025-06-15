<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Recipes;

class RecipesScore extends Model
{
    use HasFactory;
    protected $fillable = ['recipes_id', 'user_id', 'score', '
    updated_at', 'created_at'];

    /**
     * Получить рецепт к которому относится оценка
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipes::class);
    }

    /**
     * Получить пользователя который поставил оценку
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
