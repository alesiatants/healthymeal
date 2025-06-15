<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\ScheduleMonitorCommand;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        ScheduleMonitorCommand::class,
    ])
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule){
        $schedule->command('monitor:requests') //вызывает команду monitor:requests
        ->everyTwoSeconds() //делает запросы каждые 2 секунды
        ->withoutOverlapping() //не запускает несколько раз одновременно
        ->appendOutputTo(storage_path('logs/debug-monitor.log')); //добавляет вывод в файл
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
					//'role' => \App\Http\MiddlewareRoleMiddleware::class,
					'role' => \App\Http\Middleware\CheckRole::class, // проверяет роль
					'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class, // проверяет разрешение
					'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class, // проверяет роль или разрешение
				]);
                $middleware->append(\App\Http\Middleware\Cors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
