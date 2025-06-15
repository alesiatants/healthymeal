<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('is_active_category')) {
    
    /**
     * Проверяет, является ли категория активной
     *
     * @param \App\Models\ProductType|\App\Models\RecipeType $type
     * @return bool
     */
    function is_active_category($type)
    {
        $routeName = Route::currentRouteName();
        
        // Для продуктов
        if ($routeName === 'product-types.show') {
            $currentType = Route::current()->parameter('productType');
            return compare_types($currentType, $type);
        }
        
        // Для рецептов
        if ($routeName === 'recipes.index') {
            $currentType = Route::current()->parameter('recipeType');
            return compare_types($currentType, $type);
        }
        
        return false;
    }
}

if (!function_exists('compare_types')) {
    /**
     * Сравнивает два объекта/идентификатора типа
     *
     * @param mixed $current
     * @param mixed $type
     * @return bool
     */
    function compare_types($current, $type)
    {
        if ($current instanceof $type) {
            return $current->id === $type->id;
        }
        
        if (is_object($current) && !is_object($type)) {
            return $current->id == $type;
        }
        
        if (!is_object($current) && is_object($type)) {
            return $current == $type->id;
        }
        
        return $current == $type;
    }
}

if (!function_exists('menu_item_class')) {
    /**
     * Возвращает классы для пункта меню в зависимости от активности
     *
     * @param \App\Models\ProductType|\App\Models\RecipeType $item
     * @param string $activeClass
     * @param string $inactiveClass
     * @return string
     */
    function menu_item_class($item, $activeClass, $inactiveClass)
    {
        return is_active_category($item) ? $activeClass : $inactiveClass;
    }
}