<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UniquePassword implements Rule
{
    protected ?int $exceptUserId;
    protected string $messageText = 'Password ini sudah digunakan oleh user lain. Gunakan password yang berbeda.';

    public function __construct(?int $exceptUserId = null)
    {
        $this->exceptUserId = $exceptUserId;
    }

    public function passes($attribute, $value): bool
    {
        $query = User::query();

        if ($this->exceptUserId) {
            $query->where('id', '!=', $this->exceptUserId);
        }

        // Cek hash satu per satu (karena bcrypt tidak bisa query langsung)
        foreach ($query->pluck('password', 'id') as $userId => $hashedPassword) {
            if (Hash::check($value, $hashedPassword)) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return $this->messageText;
    }
}