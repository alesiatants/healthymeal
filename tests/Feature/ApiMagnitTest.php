<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Services\ProductPriceService;


class ApiMagnitTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function validates_api_magnit_connection()
    {
        $service = app(ProductPriceService::class);
        $response = $service->searchProducts("Морковь");
        //Если токен авторизации некорректный
        $this->assertEquals(401, $response->code);
        //При корректном токене и валидном запросе
        $this->assertEquals(200, $response->code);
        $this->assertNotEmpty($response->items);
        $this->assertStringContainsString('Морковь',$response->items[0]->name);
    }
}