<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipesFavorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Контроллер для работы с избранными рецептами пользователя
 */
class FavoriteController extends Controller
{
    /**
     * Добавление рецепта в избранное
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * @response {
     *   "success": true
     * }
     * @response 500 {
     *   "success": false,
     *   "error": "Error message"
     * }
     */
    public function add(Request $request)
    {
        Log::info("Добавление рецепта в избранные");
        \DB::beginTransaction();
        try {
           $validated = $request->validate([
                'recipe_id' => 'required|exists:recipes,id',
            ]);
            Log::Debug("Данные запроса для добавления рецепта в избранные:\n" . json_encode($validated, JSON_UNESCAPED_UNICODE));

            RecipesFavorite::create([
                'user_id' => Auth::id(),
                'recipes_id' => $request->input('recipe_id'),
            ]);

            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::Error("Ошибка при добавлении рецепта в избранные:\n" . $e->getMessage());
            \DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Удаление рецепта из избранного
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * @response {
     *   "success": true
     * }
     * @response 500 {
     *   "success": false,
     *   "error": "Error message"
     * }
     */
    public function remove(Request $request)
    {
        Log::info("Удаление рецепта из избранного");
        \DB::beginTransaction();
        try {
            $validated = $request->validate([
                'recipe_id' => 'required|exists:recipes_favorites,recipes_id', // Исправлено на recipes_favorites
            ]);
            Log::Debug("Данные запроса для удаления рецепта из избранного:\n" . json_encode($validated, JSON_UNESCAPED_UNICODE));

            // Удаление из избранного
            RecipesFavorite::where('user_id', Auth::id())
                ->where('recipes_id', $request->input('recipe_id'))
                ->delete();

            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::Error("Ошибка при удалении рецепта из избранных:\n" . $e->getMessage());
            \DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}