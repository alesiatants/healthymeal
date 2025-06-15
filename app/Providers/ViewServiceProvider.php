<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;

use Illuminate\Support\ServiceProvider;
use App\Models\RecipeType;
use App\Models\ProductType;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share data with all views
        view()->composer('components.leftmenu', function ($view) { 
            $recipeTypes = RecipeType::all();
            $productTypes = ProductType::all();
            $view->with([
                'productTypes' => $productTypes,
                'recipeTypes' => $recipeTypes
            ]);
        });
    }
}
