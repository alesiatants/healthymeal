<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CalculatorTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function validates_calculate_monday_calories()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'birth_date' => '1990-01-01',
            'phone' => '1234567890',
            'gender' => 'Мужской',
        ]);
        $dietologRole = Role::create(['name' => 'user']);
        $user->assignRole('user');
        $response = $this->actingAs($user)->postJson('/calculator/calculate', [
            "birthday" => "1999-01-04",
            "weight"   => 70,
            "gender"   => "female",
            "goal"     => "Набрать вес",
            "activity_level"=> "Умственный",
            "meals_per_day"=> 3
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('results');
        $result = session('results');
        $this->assertEquals(2357, $result['weeklyPlan']['Понедельник']['total']);
        $this->assertNotEmpty($result['todayPlan']);
        $this->assertCount(3, $result['todayPlan']);
    }
}