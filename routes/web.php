<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CaloriesCalculatorController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\RecipesController;
use App\Http\Controllers\RecipeCommentController;
use App\Http\Controllers\FavoriteController;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use App\Models\Products;

Route::middleware(\App\Http\Middleware\Cors::class)->group(function () {
Route::get('/', [HomeController::class, 'index'])
    ->name('home');
Route::get('/filter', [HomeController::class, 'filter'])
    ->name('home.filter');

Route::get('/products/{productType:slug}', [ProductsController::class, 'showProductType'])
    ->name('products.index');
Route::get('/products/{productType:slug}/{product:slug}', [ProductsController::class, 'show'])->name('products.show');
Route::get('/products', [ApiProductController::class, 'search'])
    ->name('products.search');

Route::get('/recipes/{recipeType:slug}/{recipe:slug}', [RecipesController::class, 'show'])
    ->name('recipes.show');

Route::get('/recipes/{recipeType:slug}', [RecipesController::class, 'index'])
    ->name('recipes.index');
Route::get('/search/product', [ProductsController::class, 'searchByName'])
->name('products.search');

Route::get('/dashboard', function () {
    $user = Auth::user();
    $roles = $user->getRoleNames();
    $permissions = $user->getAllPermissions()->pluck('name')->toArray();
    Log::info("Текущий пользователь: $user->name", [
        'user_id' => $user->id,
        'roles' => $roles,
        'permissions' => $permissions,
        'can' => in_array('edit recipe', $permissions)
    ]);
    return view('dashboard');
})->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {

    Route::get('/calculator', [CaloriesCalculatorController::class, 'show'])
    ->name('calculator.show');

    Route::post('/calculator/calculate', [CaloriesCalculatorController::class, 'calculate'])
    ->name('calculator.calculate');

    Route::post('/recipes/{recipeType:slug}/{recipe:slug}/comments', [RecipeCommentController::class, 'store'])
        ->name('recipes.comments.store');
    Route::put('/comments/{comment}', [RecipeCommentController::class, 'update'])
        ->name('comments.update');
    Route::delete('/comments/{comment}', [RecipeCommentController::class, 'destroy'])
        ->name('comments.destroy');

    Route::post('/favorites/add', [FavoriteController::class, 'add'])->name('fovirites.add');
    Route::post('/favorites/remove', [FavoriteController::class, 'remove'])
        ->name('fovirites.remove');
    Route::get('/favorites', [RecipesController::class, 'showFavoritesRecipes'])
        ->name('recipes.showfavorites');

    Route::post('/calculate-cost', [RecipesController::class, 'calculateCost'])
        ->name('recipes.calculate-cost');

    Route::post('/{recipeType:slug}/{recipe:slug}/rate',[RecipesController::class, 'storeRating'])
        ->name('recipes.rate');
    Route::put('/{recipeType:slug}/{recipe:slug}/rate', [RecipesController::class, 'updateRating'])
        ->name('recipes.rate.update');

    Route::middleware('role:dietolog')->group(function () {
        Route::get('/ownrecipes', [RecipesController::class, 'showOwnRecipes'])
            ->name('recipes.showown');
        Route::get('/ownrecipes/create', [RecipesController::class, 'create'])
            ->name('recipes.create');
        Route::post('/ownrecipes', [RecipesController::class, 'store'])
            ->name('recipes.store');
        Route::get('/ownrecipes/{recipe:slug}/edit', [RecipesController::class, 'edit'])
            ->name('recipes.edit');
        Route::put('/ownrecipes/{recipe:slug}', [RecipesController::class, 'update'])
            ->name('recipes.update');
        Route::delete('/ownrecipes/{recipe:slug}', [RecipesController::class, 'destroy'])
            ->name('recipes.destroy');
    });

    // Admin Routes Group
    Route::middleware(['auth', 'role:admin|superadmin'])->group(function () {
        Route::get('/users', [AdminController::class, 'index'])
            ->name('admin.users.index');
        Route::post('/users', [AdminController::class, 'store'])
            ->name('admin.users.store');
        Route::put('/users/{user}/update', [AdminController::class, 'update'])
            ->name('admin.users.update');
        Route::put('/users/{user}', [AdminController::class, 'activate'])
            ->name('admin.users.activate');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::get('/requests', [AdminController::class, 'showRequest'])
            ->name('admin.system.requests');
        Route::get('/applications', [AdminController::class, 'indexApplication'])
            ->name('admin.applications.index');
        Route::put('/applications/{application}', [AdminController::class, 'updateApplication'])
            ->name('admin.applications.update');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::post('/profile/plan', [ProfileController::class, 'updatePlan'])
        ->name('profile.update.plan');
    Route::post('/profile/application', [ProfileController::class, 'newApplication'])
        ->name('profile.application.store');
    Route::put('/profile/{application}/update', [ProfileController::class, 'updateApplication'])
        ->name('profile.application.update');
    Route::delete('/profile/{application}/delete', [ProfileController::class, 'deleteApplication'])
        ->name('profile.application.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

});
require __DIR__.'/auth.php';
