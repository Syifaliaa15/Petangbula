<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordRules implements Rule
{
    public function passes($attribute, $value): bool
    {
        // Minimal 8 karakter
        if (strlen($value) < 8) return false;

        // Harus ada huruf kecil
        if (!preg_match('/[a-z]/', $value)) return false;

        // Harus ada huruf besar
        if (!preg_match('/[A-Z]/', $value)) return false;

        // Harus ada angka
        if (!preg_match('/[0-9]/', $value)) return false;

        return true;
    }

    public function message(): string
    {
        return 'Password minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.';
    }
}