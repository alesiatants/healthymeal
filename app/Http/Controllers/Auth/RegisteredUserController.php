<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\StrongPassword;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Отображает форму регистрации нового пользователя.
     * 
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Обрабатывает запрос на регистрацию нового пользователя.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('Регистрация нового пользователя', [
            'name' => $request->input('name'),
            'email' => $request->input('email')]);
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:Мужской,Женский'],
            'phone' => ['required', 'regex:/^\+7-\d{3}-\d{3}-\d{2}-\d{2}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', new StrongPassword(
                minLength:10,
                requireMixedCase: true,
                requireNumbers: true,
                requireSymbols: true
            )],
        ];
    
        $messages = [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Введите корректный Email.',
            'email.unique' => 'Этот Email уже используется.',
            'birth_date.required' => 'Поле "Дата рождения" обязательно для заполнения.',
            'birth_date.date' => 'Введите корректную дату рождения.',
            'gender.required' => 'Поле "Пол" обязательно для заполнения.',
            'gender.in' => 'Выберите корректный пол.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min_length' => 'Пароль должен содержать минимум :min_length символов.',
            'password.letters' => 'Пароль должен содержать хотя бы одну букву.',
            'password.mixed_case' => 'Пароль должен содержать заглавные и строчные буквы.',
            'password.numbers' => 'Пароль должен содержать хотя бы одну цифру.',
            'password.symbols' => 'Пароль должен содержать хотя бы один спец символ.',
        ];
    
        // Добавляем кастомные сообщения для правил Password
        $validator = Validator::make($request->all(), $rules, $messages);
    
       
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        $validatedData = $validator->validated();

        if (User::where('phone', str_replace(['+', '-'], '', $validatedData['phone']))->exists()) {
            return back()->withErrors(['phone' => 'Этот номер телефона уже зарегистрирован.'])->withInput();
        }
        // Создание пользователя
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'birth_date' => $validatedData['birth_date'],
            'gender' => $validatedData['gender'],
            'phone' => str_replace(['+', '-'], '', $validatedData['phone']),
            'password' => Hash::make($validatedData['password']),
        ]);
        //прикрепление роли
        $user->assignRole('user');
    
        event(new Registered($user));
    
        Auth::login($user);
        
        return redirect(route('home', absolute: false));
   
    }
    
}
