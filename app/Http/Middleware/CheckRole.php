<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Проверяет, имеет ли пользователь определенную роль.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
			abort(403, 'Необходима авторизация.'); // если пользователь не авторизован
		}

		if (!Auth::user()->hasRole($role)) {
			abort(403, 'У вас нет доступа к этой странице.'); //  Если нет нужной роли
		}
		return $next($request);
    }
}
