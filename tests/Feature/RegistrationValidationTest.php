<?php

namespace Tests\Feature;

use App\Rules\StrongPassword;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationValidationTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function password_must_contain_at_least_10_characters()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birth_date' => "2004-10-12",
            'phone' => "+7-928-177-34-12",
            'gender'=> "male",
            'password' => 'Short1!',
            'password_confirmation' => 'Short1!',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'Пароль должен содержать минимум 10 символов.'
        ]);
    }
    #[Test]
    public function password_must_contain_special_characters()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birth_date' => "2004-10-12",
            'phone' => "+7-928-177-34-12",
            'gender'=> "male",
            'password' => 'NoSpecial1',
            'password_confirmation' => 'NoSpecial1',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'Пароль должен содержать хотя бы один специальный символ.'
        ]);
    }

    #[Test]
    public function valid_password_passes_validation()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'ValidPass1!',
            'password_confirmation' => 'ValidPass1!',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'phone' => '+7-123-456-78-90',
        ]);

        $response->assertSessionDoesntHaveErrors(['password']);
    }

    #[Test]
    public function password_must_contain_uppercase_letters()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birth_date' => "2004-10-12",
            'phone' => "+7-928-177-34-12",
            'gender'=> "male",
            'password' => 'short1!',
            'password_confirmation' => 'short1!',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Пароль должен содержать хотя бы одну заглавную букву.'
        ]);
    }

    #[Test]
    public function password_must_contain_lowercase_letters()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birth_date' => "2004-10-12",
            'phone' => "+7-928-177-34-12",
            'gender'=> "male",
            'password' => '345HJBHBDD',
            'password_confirmation' => '345HJBHBDD',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Пароль должен содержать хотя бы одну строчную букву.'
        ]);
    }

    #[Test]
    public function password_must_contain_numbers()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birth_date' => "2004-10-12",
            'phone' => "+7-928-177-34-12",
            'gender'=> "male",
            'password' => 'NoNumbers!',
            'password_confirmation' => 'NoNumbers!',
            // другие обязательные поля
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Пароль должен содержать хотя бы одну цифру.'
        ]);
    }


}

