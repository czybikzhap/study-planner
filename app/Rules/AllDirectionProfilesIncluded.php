<?php

namespace App\Rules;

use App\Models\Direction;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllDirectionProfilesIncluded implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Получаем ID направления из атрибута
        $directionId = $this->extractDirectionIdFromAttribute($attribute);

        if (!$directionId) {
            $fail('Не удалось определить направление.');
            return;
        }

        // Получаем все профили этого направления из БД
        $direction = Direction::with('profiles')->find($directionId);

        if (!$direction) {
            $fail("Направление с ID {$directionId} не найдено.");
            return;
        }

        $allProfileIds = $direction->profiles->pluck('id')->toArray();
        $submittedProfileIds = collect($value)->pluck('id')->toArray();

        // Проверяем, что все профили направления присутствуют
        $missingProfileIds = array_diff($allProfileIds, $submittedProfileIds);

        if (!empty($missingProfileIds)) {
            $missingIdsStr = implode(', ', $missingProfileIds);
            $fail("Не все профили направления включены. Отсутствуют профили с ID: {$missingIdsStr}");
        }

        // Дополнительно проверяем, что нет лишних профилей
        $extraProfileIds = array_diff($submittedProfileIds, $allProfileIds);
        if (!empty($extraProfileIds)) {
            $extraIdsStr = implode(', ', $extraProfileIds);
            $fail("Указаны профили, не принадлежащие направлению: {$extraIdsStr}");
        }
    }

    private function extractDirectionIdFromAttribute(string $attribute): ?int
    {
        // Извлекаем ID направления из пути атрибута
        // directions.0.profiles → directions.0.id
        preg_match('/directions\.(\d+)\.profiles/', $attribute, $matches);

        if (isset($matches[1])) {
            $directionIndex = $matches[1];
            return request()->input("directions.{$directionIndex}.id");
        }

        return null;
    }
}
