<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class StrongPassword implements ValidationRule
{
    private int $minLength = 8;
    private bool $requireLetters = true;
    private bool $requireMixedCase = true;
    private bool $requireNumbers = true;
    private bool $requireSymbols = true;

    public function __construct(
        int $minLength = 8,
        bool $requireMixedCase = true,
        bool $requireNumbers = true,
        bool $requireSymbols = true
    ) {
        $this->minLength = max($minLength, 1);
        $this->requireMixedCase = $requireMixedCase;
        $this->requireNumbers = $requireNumbers;
        $this->requireSymbols = $requireSymbols;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Str::length($value) < $this->minLength) {
            $fail("Пароль должен содержать минимум {$this->minLength} символов.");
        }
        if ($this->requireLetters && !preg_match('/\p{L}/u', $value)) {
            $fail('Пароль должен содержать хотя бы одну букву.');
        }
        if ($this->requireMixedCase) {
            if (!preg_match('/\p{Lu}/u', $value)) {
                $fail('Пароль должен содержать хотя бы одну заглавную букву.');
            }
            if (!preg_match('/\p{Ll}/u', $value)) {
                $fail('Пароль должен содержать хотя бы одну строчную букву.');
            }
        }
        if ($this->requireNumbers && !preg_match('/\d/', $value)) {
            $fail('Пароль должен содержать хотя бы одну цифру.');
        }
        if ($this->requireSymbols && !preg_match('/[\W_]/', $value)) {
            $fail('Пароль должен содержать хотя бы один специальный символ.');
        }
    }
}