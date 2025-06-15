<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class HeavyTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_detects_slow_responses_under_load()
    {
        $concurrentUsers = 30; // 20 "пользователей" одновременно
        $slowThreshold = 5;  // 500ms — максимально допустимое время
        $slowRequests = 0;     // Счётчик медленных запросов

        $url = url('/login');
        $client = new Client();
        $credentials = [
            "email" => "test@gmail.com",
            "password" => "test"
        ];

        // 1. Генератор запросов (для Pool)
        $requests = function () use ($url, $credentials, $concurrentUsers) {
            for ($i = 0; $i < $concurrentUsers; $i++) {
                yield new Request('POST', $url, ['Content-Type' => 'application/json'], json_encode($credentials));
            }
        };

        // 2. Настройка Pool
        $pool = new Pool($client, $requests(), [
            'concurrency' => $concurrentUsers, // Все 20 запросов запускаются сразу
            'fulfilled' => function ($response, $index) {
                // Успешный запрос (опционально)
            },
            'rejected' => function ($reason, $index) {
                // Ошибка (можно логировать, но не влияет на тест)
            },
            'options' => [
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$slowRequests, $slowThreshold) {
                    echo "Time - " . $stats->getTransferTime() . "\n";
                    if ($stats->getTransferTime() > $slowThreshold) {
                        $slowRequests++;
                    }
                }
            ]
        ]);

        // 3. Запуск и ожидание
        $pool->promise()->wait();

        // 4. Проверка: ни один запрос не должен быть медленным
        $this->assertEquals(
            0,
            $slowRequests,
            "Обнаружены медленные запросы (> " . $slowThreshold . "s): " . $slowRequests .  " из "  . $concurrentUsers
        );
    }
}