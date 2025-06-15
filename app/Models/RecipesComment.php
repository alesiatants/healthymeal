<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Recipes;

class RecipesComment extends Model
{
    use HasFactory;
    protected $fillable = ['recipe_id', 'user_id', 'comment'];

    /**
     * Получить рецепт, к которому относится комментарий
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipes::class);
    }

    /**
     * Получить пользователя, который оставил комментарий
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
