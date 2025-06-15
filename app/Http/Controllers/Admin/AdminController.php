<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\UserActivationLog;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;
use Illuminate\Validation\Rule;
use App\DTO\ErrorResponse;
use Illuminate\Support\Facades\Hash;
use Barryvdh\Debugbar\Facades\Debugbar;
use App\DTO\DebugRequest;
use DateTime;
use App\Models\Application;

/**
 * Контроллер для работы с пользователями и статистическими данными по работе системы
 */
class AdminController extends Controller
{
    //статусы функционирования пользователей
    const ACTIVATE = "activate";
    const DISACTIVATE = "deactivate";
    

    /**
     * @var SerializerInterface
     */
    //Сериализатор для работы с JSON
    private SerializerInterface $serializer;

    /**
     * Конструктор контроллера
     *
     * @param SerializerInterface $serializer
     */
    //Внедрение зависимости сериализатора
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * Отображение списка пользователей
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Spatie\QueryBuilder\Exceptions\InvalidFilterQuery
     * @throws \Spatie\QueryBuilder\Exceptions\InvalidSortQuery
     * @throws \Spatie\QueryBuilder\Exceptions\InvalidIncludesQuery
     * @throws \Spatie\QueryBuilder\Exceptions\InvalidFieldsQuery
     * @throws \Spatie\QueryBuilder\Exceptions\InvalidIncludesQuery
     */    
    public function index(Request $request)
    {
        Log::info("Фильтрация пользователей");
        $validated = $request->validate([
            'sort' => 'nullable|in:name,email,created_at,phone,gender,birth_date',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $sortField = $validated['sort'] ?? 'created_at';
        $sortDirection = $validated['direction'] ?? 'desc';

        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                'name',
                'email',
                'phone',
                'gender',
                'active',
                'birth_date',
                AllowedFilter::callback('roles', function($query, $roles) {
                    $roles = is_array($roles) ? $roles : [$roles];
                    return $query->whereHas('roles', function($q) use ($roles) {
                        $q->whereIn('name', $roles);
                    });
                }),
                AllowedFilter::scope('search', 'searchByNameOrEmail'),
            ])
            ->orderBy($sortField, $sortDirection)
            ->with('roles')
            ->paginate(5)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение нового пользователя
     * 
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */
    public function store(Request $request)
    {
        try {
            Log::info("Создание нового пользователя");
            $request['phone'] = str_replace(['+', '-'], '', $request['phone']);
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
                'phone' => ['required', "min:11", Rule::unique('users')],
                'gender' => ['required', 'in:Мужской,Женский'],
                'role' => ['required', 'in:admin,dietolog'],
                'birth_date' => ['required', 'date', 'before_or_equal:2016-12-31', 'after_or_equal:1980-01-01']
            ];
        
            $messages = [
                'name.required' => 'Поле "Имя" обязательно для заполнения.',
                'email.required' => 'Поле "Email" обязательно для заполнения.',
                'email.email' => 'Введите корректный Email.',
                'email.unique' => 'Этот Email уже используется.',
                'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
                'phone.unique' => 'Этот телефон уже используется.',
                'phone.min' => 'Неверный формат поля "Телефон"',
                'birth_date.required' => 'Поле "Дата рождения" обязательно для заполнения.',
                'birth_date.before_or_equal' => 'Дата выходит за пределы допустимой, выберите раннюю дату!',
                'birth_date.after_or_equal' => 'Дата выходит за пределы допустимой, выберите более позднюю дату!',
            ];
        
            $validated = $request->validate($rules, $messages);
            Log::debug("Данные нового пользователя: \n" . json_encode($validated,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    
            DB::beginTransaction();
             
             // Обновляем данные пользователя
             $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'password' => Hash::make($validated['email']),
                'birth_date' => $validated['birth_date'],
                'active' => true
             ]);
             $user->assignRole($validated['role']);
             
             DB::commit();
             
             return new JsonResponse([
                'success' => true,
                "message" => "Пользователь успешно добавлен!"
            ], JsonResponse::HTTP_OK);
             
         }  catch (ValidationException $e) {
            Log::error('Ошибка при добавлении пользователя: ' . $e->getMessage());
            $flatErrors = [];
            foreach ($e->errors() as $field => $messages) {
               $newField = str_replace('.', '[',$field);
               $newField = preg_replace('/\[(\w+)$/', '][$1]', $newField);
               $flatErrors[$newField] = $messages[0];
            }    
             $error = new ErrorResponse(false, $flatErrors, "");
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
            return $response;
           
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при добавлении пользователя: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при добавлении пользователя: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Обновление данных пользователя
     * 
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */
    public function update(Request $request, User $user)
    {
        try {
            Log::info("Обновление пользователя");
            $request['phone'] = str_replace(['+', '-'], '', $request['phone']);
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone' => ['required', "min:11", Rule::unique('users')->ignore($user->id)]
            ];
        
            $messages = [
                'name.required' => 'Поле "Имя" обязательно для заполнения.',
                'email.required' => 'Поле "Email" обязательно для заполнения.',
                'email.email' => 'Введите корректный Email.',
                'email.unique' => 'Этот Email уже используется.',
                'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
                'phone.unique' => 'Этот телефон уже используется.',
                'phone.min' => 'Неверный формат поля "Телефон"'
            ];
        
            // Валидация данных
            $validated = $request->validate($rules, $messages);
            Log::debug("Данные обновляемого пользователя: \n" . json_encode($validated,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    
            DB::beginTransaction();
             
             // Обновляем данные пользователя
             $user->update([
                 'name' => $validated['name'],
                 'phone' => $validated['phone'],
                 'email' => $validated['email'],
                 'updated_at' => now(),
             ]);
             
             DB::commit();
             
             return new JsonResponse([
                'success' => true,
                "message" => "Пользователь успешно обновлен!"
            ], JsonResponse::HTTP_OK);
             
         }  catch (ValidationException $e) {
            Log::error('Ошибка при обновлении пользователя: ' . $e->getMessage());
            $flatErrors = [];
            foreach ($e->errors() as $field => $messages) {
               $newField = str_replace('.', '[',$field);
               $newField = preg_replace('/\[(\w+)$/', '][$1]', $newField);
               $flatErrors[$newField] = $messages[0];
            }    
             $error = new ErrorResponse(false, $flatErrors, "");
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST);
            return $response;
           
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при обновлении пользователя: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при обновлении пользователя: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
    }

    public function destroy(User $user)
    {
        Log::info("Деактивация пользователя");
        try {
            if(!$user->active) {
                throw new Exception("Пользователь уже дективирован!");}
            DB::transaction(function () use ($user) {
                $user->update(['active' => false]);
                UserActivationLog::create([
                    'user_id' => $user->id,
                    'action_by' => auth()->id(),
                    'action' => self::DISACTIVATE,]); });
            return new JsonResponse([
                'success' => true,
                "message" => "Пользователь успешно деактивирован!"
            ], JsonResponse::HTTP_OK);
         } catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при деактивации пользователя: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при деактивации пользователя: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response; } }
    public function activate(User $user)
    {
        Log::info("Активация пользователя");
        try {
            if($user->active) {
                throw new Exception("Пользователь уже активирован!"); }
            DB::transaction(function () use ($user) {
                $user->update(['active' => true]);
                UserActivationLog::create([
                    'user_id' => $user->id,
                    'action_by' => auth()->id(),
                    'action' => self::ACTIVATE, ]); }); 
            return new JsonResponse([
                'success' => true,
                "message" => "Пользователь успешно активирован!"
            ], JsonResponse::HTTP_OK);   
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при активации пользователя: ' . $e->getMessage());
            $error = new ErrorResponse(false, [], 'Ошибка при активации пользователя: ' . $e->getMessage());
            $response = new JsonResponse($this->serializer->serialize($error, 'json'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }
    }
    /**
     * Отображение данных запросов с логами
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Exception
     * @throws \JMS\Serializer\Exception\RuntimeException
     * @throws \JMS\Serializer\Exception\InvalidArgumentException
     * @throws \JMS\Serializer\Exception\ParseException
     * @throws \JMS\Serializer\Exception\LogicException
     * @throws \JMS\Serializer\Exception\UnsupportedFormatException
     */
    public function showRequest(Request $request)
    {
        Log::info("Фильтрация запросов");
        $filters = $this->prepareFilters($request);
        $max = $request->input('max', 5);
        $offset = $request->input('offset', 0);
        $storage = Debugbar::getStorage();
        $debugData = $storage->find($filters, $max, $offset);
        $filteredData = array_filter($debugData, function($item) use ($request) {
            if ($uri = $request->input('uri')) {
                if (!str_contains(strtolower($item['uri']),strtolower($uri))) {
                    return false;}}
            if ($dateFrom = $request->input('date_from')) {
                if (($item['datetime'] ? new DateTime($item['datetime']) : '') < new DateTime($dateFrom)) {
                    return false;}}
            if ($dateTo = $request->input('date_to')) {
                if (($item['datetime'] ? new DateTime($item['datetime']) : '') > new DateTime($dateTo)) {
                    return false;}}
            if ($minDuration = $request->input('min_duration')) {
                if (($item['time']['duration'] ?? 0) < $minDuration) {
                    return false;}}
            if ($maxDuration = $request->input('max_duration')) {
                if (($item['time']['duration'] ?? 0) > $maxDuration) {
                    return false;}}
            if ($logTypeFilter = $request->input("log_type")) {
                $hasMatchingLog = false;
                foreach ($item['messages']['messages'] ?? [] as $message) {
                    if (($message['label'] ?? null) === $logTypeFilter) {
                    $hasMatchingLog = true;
                    break;}}
                if (!$hasMatchingLog) {
                    return false;}}  
            return true;});
        $requests = array_map(function($item) {
            return $this->serializer->fromArray($item, DebugRequest::class);
        }, $filteredData);
        return view ('admin.system.requests', compact("requests"));}
    
    /**
     * Подготовка фильтров для запросов
     * 
     * @param Request $request
     * @return array
     */
    private function prepareFilters(Request $request): array
    {
        $filters = [];

        // Фильтр по методу
        if ($method = $request->input('method')) {
            $filters['method'] = strtoupper($method);
        }
        return $filters;
    }

    /**
     * Отображение списка заявок
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function indexApplication(Request $request)
    {
        Log::info("Фильтрация заявок");
        $applications = Application::with('user')
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->email, fn($q, $email) => $q->whereHas('user', fn($q) => $q->where('email', 'like', "%$email%")))
            ->when($request->date, fn($q, $date) => $q->whereDate('created_at', $date))
            ->latest()
            ->paginate(10);

        return view('admin.applications.application', compact('applications'));
    }

    /**
     * Обновление статуса заявки
     * 
     * @param Request $request
     * @param Application $application
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function updateApplication(Request $request, Application $application)
    {
        Log::info("Обновление статуса заявки");
        $request->validate([
            'status' => 'required|in:В обработке,Подтверждена,Отклонена',
        ]);
        if ($request->status == "Отклонена" && (!$request->comment || strlen($request->comment) < 10)) {
            throw ValidationException::withMessages([
                'comment' => 'Укажите причину отказа.'
            ]);
        }
        $updateData = [
            'status' => $request->status,
            'admin_comment' => $request->comment,
        ];
        
        if ($application->status !== $request->status) {
            $updateData['admin_id'] = auth()->id();
        }

        $application->update($updateData);

        if ($request->status === 'Подтверждена') {
            $application->user->assignRole('dietolog');
        }

        return back()->with('success', 'Статус заявки обновлен');
    }
}
