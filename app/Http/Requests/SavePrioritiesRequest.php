<?php

namespace App\Http\Requests;

use App\Rules\AllDirectionProfilesIncluded;
use App\Rules\ProfileBelongsToDirection;
use App\Rules\ProfilePrioritiesSequence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SavePrioritiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'directions' => 'required|array|min:1',
            'directions.*.id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('directions', 'id')
            ],
            'directions.*.priority' => 'required|integer|min:1',
            'directions.*.profiles' => 'required|array',
            'directions.*.profiles.*.id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('profiles', 'id')
            ],
            'directions.*.profiles.*.priority' => 'required|integer|min:1',
        ];
    }

    /**
     * Дополнительная валидация после основных правил
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $directions = $this->input('directions', []);

                // Проверка направлений
                $this->validateDirections($directions, $validator);

                // Проверка профилей
                $this->validateProfiles($directions, $validator);
            }
        ];
    }

    /**
     * Проверка приоритетов направлений
     */
    private function validateDirections(array $directions, Validator $validator): void
    {
        if (empty($directions)) {
            return;
        }

        // Проверка 1: Уникальность приоритетов направлений
        $directionPriorities = collect($directions)->pluck('priority')->toArray();
        $uniqueDirectionPriorities = array_unique($directionPriorities);

        if (count($directionPriorities) !== count($uniqueDirectionPriorities)) {
            $duplicates = $this->findDuplicates($directionPriorities);
            $duplicatesStr = implode(', ', $duplicates);

            $validator->errors()->add(
                'directions',
                "Приоритеты направлений не должны повторяться. Найдены дубликаты: {$duplicatesStr}"
            );
        }

        // Проверка 2: Последовательность приоритетов направлений (1, 2, 3...)
        $sortedDirectionPriorities = collect($directionPriorities)->sort()->values()->toArray();
        $expectedDirectionPriorities = range(1, count($directionPriorities));

        if ($sortedDirectionPriorities !== $expectedDirectionPriorities) {
            $currentStr = implode(', ', $sortedDirectionPriorities);
            $expectedStr = implode(', ', $expectedDirectionPriorities);

            $validator->errors()->add(
                'directions',
                "Приоритеты направлений должны начинаться с 1 и идти по порядку без пропусков. Текущие: [{$currentStr}], ожидаемые: [{$expectedStr}]"
            );
        }

        // Проверка 3: Принадлежность профилей к направлениям
        foreach ($directions as $directionIndex => $direction) {
            $directionId = $direction['id'];
            $profiles = $direction['profiles'] ?? [];

            foreach ($profiles as $profileIndex => $profile) {
                $profileId = $profile['id'];

                $profileBelongsToDirection = \App\Models\Profile::where('id', $profileId)
                    ->where('direction_id', $directionId)
                    ->exists();

                if (!$profileBelongsToDirection) {
                    $validator->errors()->add(
                        "directions.{$directionIndex}.profiles.{$profileIndex}.id",
                        "Профиль с ID {$profileId} не принадлежит направлению с ID {$directionId}"
                    );
                }
            }
        }
    }

    /**
     * Проверка приоритетов профилей
     */
    private function validateProfiles(array $directions, Validator $validator): void
    {
        foreach ($directions as $directionIndex => $direction) {
            $directionId = $direction['id'];
            $profiles = $direction['profiles'] ?? [];

            if (empty($profiles)) {
                continue;
            }

            $profilePriorities = collect($profiles)->pluck('priority')->toArray();

            // Проверка 1: Уникальность приоритетов профилей в пределах направления
            $uniqueProfilePriorities = array_unique($profilePriorities);

            if (count($profilePriorities) !== count($uniqueProfilePriorities)) {
                $duplicates = $this->findDuplicates($profilePriorities);
                $duplicatesStr = implode(', ', $duplicates);

                $validator->errors()->add(
                    "directions.{$directionIndex}.profiles",
                    "В направлении ID {$directionId} приоритеты профилей не должны повторяться. Найдены дубликаты: {$duplicatesStr}"
                );
            }

            // Проверка 2: Последовательность приоритетов профилей (1, 2, 3...)
            $sortedProfilePriorities = collect($profilePriorities)->sort()->values()->toArray();
            $expectedProfilePriorities = range(1, count($profilePriorities));

            if ($sortedProfilePriorities !== $expectedProfilePriorities) {
                $currentStr = implode(', ', $sortedProfilePriorities);
                $expectedStr = implode(', ', $expectedProfilePriorities);

                $validator->errors()->add(
                    "directions.{$directionIndex}.profiles",
                    "В направлении ID {$directionId} приоритеты профилей должны начинаться с 1 и идти по порядку без пропусков. Текущие: [{$currentStr}], ожидаемые: [{$expectedStr}]"
                );
            }

            // Проверка 3: Все профили направления включены
            $this->validateAllProfilesIncluded($directionId, $profiles, $directionIndex, $validator);
        }
    }

    /**
     * Проверка что все профили направления включены
     */
    private function validateAllProfilesIncluded(int $directionId, array $profiles, int $directionIndex, Validator $validator): void
    {
        $direction = \App\Models\Direction::with('profiles')->find($directionId);

        if (!$direction) {
            return;
        }

        $allProfileIds = $direction->profiles->pluck('id')->toArray();
        $submittedProfileIds = collect($profiles)->pluck('id')->toArray();

        // Проверяем, что все профили направления присутствуют
        $missingProfileIds = array_diff($allProfileIds, $submittedProfileIds);

        if (!empty($missingProfileIds)) {
            $missingIdsStr = implode(', ', $missingProfileIds);
            $validator->errors()->add(
                "directions.{$directionIndex}.profiles",
                "Для направления '{$direction->name}' не все профили включены. Отсутствуют: {$missingIdsStr}"
            );
        }

        // Проверяем, что нет лишних профилей
        $extraProfileIds = array_diff($submittedProfileIds, $allProfileIds);
        if (!empty($extraProfileIds)) {
            $extraIdsStr = implode(', ', $extraProfileIds);
            $validator->errors()->add(
                "directions.{$directionIndex}.profiles",
                "Для направления '{$direction->name}' указаны лишние профили: {$extraIdsStr}"
            );
        }
    }

    /**
     * Находит дубликаты в массиве
     */
    private function findDuplicates(array $array): array
    {
        $counts = array_count_values($array);
        $duplicates = [];

        foreach ($counts as $value => $count) {
            if ($count > 1) {
                $duplicates[] = $value;
            }
        }

        return $duplicates;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Ошибки валидации',
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'directions.required' => 'Список направлений обязателен для заполнения',
            'directions.array' => 'Направления должны быть представлены в виде массива',
            'directions.min' => 'Должно быть указано хотя бы одно направление',

            'directions.*.id.required' => 'ID направления обязательно для заполнения',
            'directions.*.id.integer' => 'ID направления должен быть целым числом',
            'directions.*.id.min' => 'ID направления должен быть положительным числом',

            'directions.*.priority.required' => 'Приоритет направления обязателен для заполнения',
            'directions.*.priority.integer' => 'Приоритет направления должен быть целым числом',
            'directions.*.priority.min' => 'Приоритет направления должен быть положительным числом',

            'directions.*.profiles.array' => 'Профили должны быть представлены в виде массива',

            'directions.*.profiles.*.id.required' => 'ID профиля обязательно для заполнения',
            'directions.*.profiles.*.id.integer' => 'ID профиля должен быть целым числом',
            'directions.*.profiles.*.id.min' => 'ID профиля должен быть положительным числом',

            'directions.*.profiles.*.priority.required' => 'Приоритет профиля обязателен для заполнения',
            'directions.*.profiles.*.priority.integer' => 'Приоритет профиля должен быть целым числом',
            'directions.*.profiles.*.priority.min' => 'Приоритет профиля должен быть положительным числом',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'directions' => 'направления',
            'directions.*.id' => 'ID направления',
            'directions.*.priority' => 'приоритет направления',
            'directions.*.profiles' => 'профили',
            'directions.*.profiles.*.id' => 'ID профиля',
            'directions.*.profiles.*.priority' => 'приоритет профиля',
        ];
    }
}
