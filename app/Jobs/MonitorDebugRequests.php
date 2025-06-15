<?php

namespace App\Jobs;

use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\SlowRequestsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class MonitorDebugRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function handle()
    {
        $this->initDebugBar();    
        try {
            $now = Carbon::now();
            $fiveSecondsAgo = $now->copy()->subSeconds(2);
            $storage = Debugbar::getStorage();   
            if (!$storage) {
                throw new \RuntimeException('DebugBar не инициализирован'); }
            $requests = $storage->find([], 5);
            Log::info('Запросы DebugBar получены', ['count' => count($requests), 'data' => $requests]);
            $slowRequests = $this->filterSlowRequests($requests, $fiveSecondsAgo, $now);
            Log::info('Медленные запросы DebugBar получены', ['count' => count($slowRequests), 'data' => $slowRequests]);
            if (!empty($slowRequests)) {
                $this->notifyAdmins($slowRequests);} 
        } catch (\Throwable $e) {
            Log::error('Мониторинг DebugBar завершился с ошибкой', [
                'error' => $e->getMessage()]);
            throw $e;} }
    protected function filterSlowRequests(array $requests, Carbon $from, Carbon $to): array
    {
        return array_filter($requests, function($request) use ($from, $to) {
            try {
                if (!isset($request['datetime'], $request['time']['duration'])) { return false;}
                $requestDate = Carbon::parse($request['datetime']);
                $duration = (float)($request['time']['duration'] ?? 0);   
                return $requestDate->between($from, $to) && $duration > 1;
            } catch (\Throwable $e) {
                Log::warning('Ошибка парсинга запроса: ' . json_encode($request));
                return false;}});}
    protected function notifyAdmins(array $requests): void
    {
        try {
            Log::info('Уведомление администраторов о медленных запросах');
            $admins = User::role('admin') ->whereNotNull('email')->get(['id', 'email', 'name']);
            if ($admins->isEmpty()) {
                Log::warning('Нет администраторов для уведомления');return;}
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new SlowRequestsNotification($requests)); } 
            Log::info('Письма направлены ' . $admins->count() . ' администраторам');  
        } catch (\Throwable $e) {
            Log::error('Ошибка уведомления администраторов: ' . $e->getMessage());}}
    
    /**
     * Инициализация DebugBar
     *
     * @return void
     */
    protected function initDebugBar(): void
    {
        if (!app()->bound('debugbar')) {
            $this->registerDebugBar();
        }
        
        if (!Debugbar::isEnabled()) {
            Debugbar::enable();
        }
    }
    
    /**
     * Регистрация DebugBar
     *
     * @return void
     */
    protected function registerDebugBar(): void
    {
        // Копируем код из вашего DebugBarServiceProvider
        $debugbar = new BaseDebugBar();
        
        // Настройка хранилища (адаптируйте под вашу конфигурацию)
        $storagePath = storage_path('debugbar');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
        
        $debugbar->setStorage(new \DebugBar\Storage\FileStorage($storagePath));
        
        // Регистрируем в приложении
        app()->instance('debugbar', $debugbar);
        app()->alias('debugbar', BaseDebugBar::class);
    }
    
    /**
     * Фильтруем медленные запросы
     *
     * @param array $requests
     * @param Carbon $from
     * @param Carbon $to
     * @return array
     */
    protected function filterSlowRequests(array $requests, Carbon $from, Carbon $to): array
    {
        return array_filter($requests, function($request) use ($from, $to) {
            try {
                if (!isset($request['datetime'], $request['time']['duration'])) {
                    return false;
                }
                $requestDate = Carbon::parse($request['datetime']);
                $duration = (float)($request['time']['duration'] ?? 0);
                
                return $requestDate->between($from, $to) && $duration > 1;
            } catch (\Throwable $e) {
                Log::warning('Ошибка парсинга запроса: ' . json_encode($request));
                return false;
            }
        });
    }
    
    /**
     * Уведомляем администраторов о медленных запросах
     *
     * @param array $requests
     * @return void
     */
    protected function notifyAdmins(array $requests): void
    {
        try {
            Log::info('Уведомление администраторов о медленных запросах');
            $admins = User::role('admin')
                ->whereNotNull('email')
                ->get(['id', 'email', 'name']);
            
            if ($admins->isEmpty()) {
                Log::warning('Нет администраторов для уведомления');
                return;
            }
            
            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->queue(new SlowRequestsNotification($requests));
            }
            
            Log::info('Письма направлены ' . $admins->count() . ' администраторам');
            
        } catch (\Throwable $e) {
            Log::error('Ошибка уведомления администраторов: ' . $e->getMessage());
        }
    }
}