<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProfilePrioritiesSequence implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Профили должны быть массивом.');
            return;
        }

        $priorities = collect($value)->pluck('priority')->sort()->values()->toArray();
        $expectedPriorities = range(1, count($priorities));

        if ($priorities !== $expectedPriorities) {
            $currentStr = implode(', ', $priorities);
            $expectedStr = implode(', ', $expectedPriorities);
            $fail("Приоритеты профилей должны начинаться с 1 и идти по порядку. Текущие: [{$currentStr}], ожидаемые: [{$expectedStr}]");
        }
    }
}
