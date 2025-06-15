<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;


class AuthenticatedSessionController extends Controller
{
    /**
     * Отобразить форму входа в систему.
     * 
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Создать новую аутентифицированную сессию.
     * 
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Database\QueryException
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\RelationNotFoundException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        Log::info("Авторизация пользователя: {$request->email}");
        try {
            $request->authenticate();

            $request->session()->regenerate();
            if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin')) {
                return redirect()->to(route('admin.users.index', absolute: false));
            } else {
                return redirect()->intended($request->input('redirect',route('home', absolute: false)));
            }
        } catch (\Exception $e) {
            Log::error("Ошибка авторизации пользователя: {$request->email} - {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Удалить аутентифицированную сессию.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Database\QueryException
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\RelationNotFoundException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function destroy(Request $request): RedirectResponse
    {
        Log::info("Выход пользователя: {$request->user()->email}");
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect()->intended($request->input('redirect',route('home', absolute: false)));
        } catch (\Exception $e) {
            Log::error("Ошибка выхода пользователя: {$request->user()->email} - {$e->getMessage()}");
            throw $e;
        }
    }
}
