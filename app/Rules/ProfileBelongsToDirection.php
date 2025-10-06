<?php

namespace App\Rules;

use App\Models\Profile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProfileBelongsToDirection implements ValidationRule
{
    public function __construct(
        private int $directionId
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $profileExists = Profile::where('id', $value)
            ->where('direction_id', $this->directionId)
            ->exists();

        if (!$profileExists) {
            $fail("Профиль с ID {$value} не принадлежит направлению с ID {$this->directionId}.");
        }
    }
}
