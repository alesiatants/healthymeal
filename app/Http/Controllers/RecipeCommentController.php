<?php

namespace App\Http\Controllers;

use App\Models\Recipes;
use App\Models\RecipesComment;
use App\Models\RecipeType;
use Exception;
use App\DTO\ErrorResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Censure\Censure;

/**
 * Этоти контроллер отвечает за управление комментариями к рецептам.
 */
class RecipeCommentController extends Controller
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * Конструктор класса.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * Метод для создания нового комментария к рецепту.
     *
     * @param Request $request
     * @param RecipeType $recipeType
     * @param Recipes $recipe
     * @return JsonResponse
     */
    public function store(Request $request, RecipeType $recipeType, Recipes $recipe)
    {        
        try {
            Log::info("Создание комментария");
            $rules = ['comment' => 'required|string|min:5|max:1000'];
            $messages = [
            'comment.required' => 'Укажите комментарий.',
            'comment.min' => 'Поле комментария должно содержать не менее 5 символов!',
            'comment.max' => 'Поле комментария должно содержать не более 1000 символов!'
            ];
            $validatedData = $request->validate($rules, $messages);
        
            $testLexicon = Censure::false_if_no_bad_words($validatedData['comment']);

            if ($testLexicon === true) {
                $error = new ErrorResponse(false, ["comment" => "Обнаружено матерное слово"], "");
                $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
                return $response;
            }
          
            DB::beginTransaction();
            
            $comment = $recipe->comments()->create([
                'comment' => $validatedData['comment'],
                'user_id' => Auth::id()
            ]);
            DB::commit();
    
            return new JsonResponse([
                'success' => true,
                'comment' => $comment->load('user'),
                "message" => "Комментарий успешно добавлен!"
            ], JsonResponse::HTTP_OK);
            
        }  catch (ValidationException $e) {
            Log::error('Ошибка при добавлении комментария: ' . $e->getMessage());
            $flatErrors = [];
            foreach ($e->errors() as $field => $messages) {
               $flatErrors[$field] = $messages[0];
            } 
            $error = new ErrorResponse(false, $flatErrors, "");
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
            return $response;
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при добавлении комментария: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при добавлении комментария: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
    }

    /**
     * Метод для обновления комментария к рецепту.
     *
     * @param Request $request
     * @param RecipeType $recipeType
     * @param Recipes $recipe
     * @param RecipesComment $comment
     * @return JsonResponse
     */
    public function update(Request $request, RecipeType $recipeType, Recipes $recipe, RecipesComment $comment)
    {
        try {
            Log::info("Обновление комментария");
            $rules = ['comment' => 'required|string|min:5|max:1000'];
            $messages = [
                'comment.required' => 'Укажите комментарий.',
                'comment.min' => 'Поле комментария должно содержать не менее 5 символов!',
                'comment.max' => 'Поле комментария должно содержать не более 1000 символов!'
            ];
            $validatedData = $request->validate($rules, $messages);
            $testLexicon = Censure::false_if_no_bad_words($validatedData['comment']);

            if ($testLexicon === true) {
                $error = new ErrorResponse(false, ["comment" => "Обнаружено матерное слово"], "");
                $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
                return $response;
            }
        
            DB::beginTransaction();
            
            $comment->update(['comment' => $validatedData['comment']]);
            
            DB::commit();
            
            return new JsonResponse([
                'success' => true,
                'comment' => $comment->load('user'),
                "message" => "Комментарий успешно обновлен!"
            ], JsonResponse::HTTP_OK);
            
        } catch (ValidationException $e) {
            Log::error('Ошибка при обновлении комментария: ' . $e->getMessage());
            $flatErrors = [];
            foreach ($e->errors() as $field => $messages) {
               $flatErrors[$field] = $messages[0];
            } 
            $error = new ErrorResponse(false, $flatErrors, "");
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
            return $response;
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при добавлении комментария!: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при обновлении комментария: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
    }

    /**
     * Метод для удаления комментария к рецепту.
     *
     * @param RecipeType $recipeType
     * @param Recipes $recipe
     * @param RecipesComment $comment
     * @return JsonResponse
     */
    public function destroy(RecipeType $recipeType, Recipes $recipe, RecipesComment $comment)
    {
        try {
            Log::info("Удаление комментария");
            DB::beginTransaction();
            
            $comment->delete();
            
            DB::commit();
            
            return new JsonResponse([
                'success' => true,
                "message" => "Комментарий успешно удален!"
            ], JsonResponse::HTTP_OK);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при удалении комментария: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при удалении комментария: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
    }
}