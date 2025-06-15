<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'birth_date' => '1990-01-01',
            'phone' => '1234567890',
            'gender' => 'Мужской',
        ]);
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'birth_date' => '1992-02-02',
            'phone' => '0987654321',
            'gender' => 'Женский',
        ]);
        User::factory()->create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'birth_date' => '1985-03-03',
            'phone' => '1122334455',
            'gender' => 'Женский',
        ]);
        User::factory()->create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'birth_date' => '1988-04-04',
            'phone' => '5566778899',
            'gender' => 'Мужской',
        ]);
        $this->call(RoleSeeder::class);
        $user = User::find(1);
        $user->assignRole('admin');
        $user = User::find(2);
        $user->assignRole('dietolog');
        $user = User::find(3);
        $user->assignRole('user');
        $user = User::find(4);
        $user->assignRole('user');
        /*
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
    }
}
