<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\UserPlans;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Application;

/**
 * Этот контроллер управляет профилем пользователя.
 */
class ProfileController extends Controller
{
    /**
     * Путь к папке с изображениями пользователей.
     * @var string
     */
    const IMAGE_PATH = "/var/www/healthymeal/healthymeal/public/storage/users/";

    /**
     * Путь к папке с документами пользователей.
     * @var string
     */
    const DOC_PATH = "/var/www/healthymeal/healthymeal/public/storage/documents/";
    
    /**
     * Отображает форму редактирования профиля пользователя.
     * 
     * @param Request $request
     * @return View 
     */
    public function edit(Request $request): View
    {
        $currentPlan = $request->user()->currentPlan();
        $plans = $request->user()->plans()->take(10)->get();
        $applications = $request->user()->dietitianApplications()->latest()->get();
        Log::info($plans);
        return view('profile.edit', [
            'currentPlan' => $currentPlan,
            'plans' => $plans,
            'user' => $request->user(),
            'applications' => $applications
        ]);
    }

    /**
     * Отображает форму создания новой заявки.
     * 
     * @param Request $request
     * @return View 
     */
    public function newApplication(Request $request)
    {
        $request->validate([
            'document' => 'required|image|max:2048',
        ]);
        $image = $request->file('document');
        $user = Auth::user();
        $slug = Str::slug($user->name);
        $documentName = $slug . ".jpg";
        $path = $image->move(self::DOC_PATH, $documentName);

        $application = Auth::user()->dietitianApplications()->create([
            'document' => "documents/" . $documentName,
            'status' => 'Новая',
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Обновляет заявку пользователя.
     * @param Request $request
     * @param Application $application
     * @return RedirectResponse
     */
    public function updateApplication(Request $request, Application $application)
    {
        $request->validate([
            'document' => 'required|image|max:2048',
        ]);
        $image = $request->file('document');
        $user = Auth::user();
        $slug = Str::slug($user->name);
        $documentName = $slug . ".jpg";
        $path = $image->move(self::DOC_PATH, $documentName);

        $application->update([
            'document' => "documents/" . $documentName,
        ]);

        return Redirect::route('profile.edit')->with('success', 'Заявка успешно обновлена!');
    }

    /**
     * Удаляет заявку пользователя.
     * @param Application $application
     * @return RedirectResponse
     */
    public function deleteApplication(Application $application)
    {
        if (preg_match('/[^\/]+$/', $application->document, $matches)) {
            $oldImageName = $matches[0];
            unlink(self::DOC_PATH . $oldImageName);
        }
        $application->delete();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    /**
     * Обновляет профиль пользователя.
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Log::info("Обновление профиля пользователя", [
            'user_id' => $request->user()->id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);
        $request->user()->fill($request->validated());
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^\+7-\d{3}-\d{3}-\d{2}-\d{2}$/', 'unique:users,phone'],
            'image' => ['nullable', 'image', 'max:2048']
        ];
    
        $messages = [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный Email.',
            'email.unique' => 'Этот Email уже используется.',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.regex' => 'Телефон указан неверно.',
            'phone.unique' => 'Этот телефон уже используется.',
        ];
    
        // Валидация данных
        $validatedData = $request->validate($rules, $messages);
        if (User::where('email', $validatedData['email'])->where('id', '!=', Auth::id())->exists()) {
            return back()->withErrors(['email' => 'Эта почта уже зарегистрированна.'])->withInput();
        }    
        if (User::where('phone', str_replace(['+', '-'], '', $validatedData['phone']))->where('id', '!=', Auth::id())->exists()) {
            return back()->withErrors(['phone' => 'Этот номер телефона уже зарегистрирован.'])->withInput();
        }    

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $request->user()->phone = str_replace(['+', '-'], '', $request->user()->phone );
        if ($request->hasFile('image')) {
            $avatar = $request->file('image');
            $avatarName = Str::slug($request->user()->name) . ".jpg";
            $path = $avatar->move(self::IMAGE_PATH, $avatarName);
            $request->user()->avatar = 'users/' . $avatarName;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Обновляет план пользователя.
     * @param Request $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePlan(Request $request)
    {
        Log::info("Обновление плана пользователя");
        $user = Auth::user();
        $currentPlan = $user->currentPlan();
        $rules = [
            'weight' => ['required', 'numeric', 'min:35', 'max:200'],
            'goal' => ['required', Rule::in(['Набрать вес', 'Сбросить вес', 'Поддержать вес'])],
            'activity_level' => ['required', Rule::in(['Умственный', 'Лёгкий', 'Средний', 'Тяжёлый', 'Сверхтяжёлый'])],
            'meals_per_day' => ['required', Rule::in([3, 4, 5])],
        ];
    
        $messages = [
            'weight.required' => 'Поле "Вес" обязателен для заполнения.',
            'goal.required' => 'Поле "Цель" обязателен для заполнения.',
        ];
    
        // Валидация данных
        $validatedData = $request->validate($rules, $messages);
        $newPlan = UserPlans::create([
            'user_id' => $user->id,
            'weight' => $validatedData['weight'],
            'goal' => $validatedData['goal'],
            'activity_level' => $validatedData['activity_level'],
            'meals_per_day' => $validatedData['meals_per_day']
        ]);
        $notification = $this->checkPlanProgress($newPlan, $currentPlan);
        return Redirect::route('profile.edit')
        ->with('status', 'profile-updated')
        ->with('notification', $notification);
    }


    /**
     * Проверяет прогресс плана пользователя.
     * @param UserPlans $newPlan
     * @param UserPlans|null $currentPlan
     * @return array
     */
    private function checkPlanProgress($newPlan, $currentPlan)
    {
        if (!$currentPlan) {
            return [
                'message' => 'План успешно создан!',
                'type' => 'success']; }
        $currentWeight = $newPlan->weight;
        $lastWeight = $currentPlan->weight;
        $goal = $newPlan->goal;
        if ($goal === 'Сбросить вес' && ($currentWeight - $lastWeight) > 1) {
            return [
                'message' => 'Вы не сбрасываете вес согласно плану. Хотите изменить цель?',
                'type' => 'warning' ];} 
        if ($goal === 'Набрать вес' && ($currentWeight - $lastWeight) < 1) {
            return [
                'message' => 'Вы не набираете вес согласно плану. Хотите изменить цель?',
                'type' => 'warning' ]; }
        if ($goal === 'Поддержать вес' && abs($currentWeight - $lastWeight) > 5) {
            return [
                'message' => 'Ваш вес выходит за пределы ±5 кг от цели. Хотите изменить план?',
                'type' => 'warning' ];}
        if (abs($currentWeight - $lastWeight) > 10) {
            return [
                'message' => 'Ваш вес слишком резко изменился. Хотите изменить план?',
                'type' => 'warning']; }
        return [
            'message' => 'Вы двигаетесь в правильном направлении! Так держать!',
            'type' => 'success'];
 }

    /**
     * Удаляет профиль пользователя.
     * @param Request $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
