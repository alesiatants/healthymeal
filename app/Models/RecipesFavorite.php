<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipesFavorite extends Model
{
    
     // Указываем, что это связующая таблица
     protected $table = 'recipes_favorites';

     // Указываем, что мы не будем использовать временные метки (created_at и updated_at)
     public $timestamps = false;
 
     // Указываем, какие поля можно массово заполнять
     protected $fillable = [
         'user_id',
         'recipes_id',
     ];
    
}
