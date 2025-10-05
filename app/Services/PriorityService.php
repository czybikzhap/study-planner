<?php

namespace App\Services;

use App\DTO\SavePrioritiesDTO;
use App\Models\Direction;
use App\Models\UserDirection;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PriorityService
{
    public function getAllDirectionsWithProfiles()
    {
        return Direction::with('profiles')->get();
    }

    // Обновленный метод с DTO
    public function saveUserPriorities(SavePrioritiesDTO $dto): array
    {
        try {
            DB::beginTransaction();

            $this->deleteUserPriorities($dto->userId);

            $savedDirections = $this->saveDirectionsPriorities($dto);
            $savedProfiles = $this->saveProfilesPriorities($dto);

            DB::commit();

            Log::info('User priorities saved successfully', [
                'user_id' => $dto->userId,
                'directions_count' => $savedDirections,
                'profiles_count' => $savedProfiles
            ]);

            return [
                'success' => true,
                'saved_directions' => $savedDirections,
                'saved_profiles' => $savedProfiles
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving user priorities', [
                'user_id' => $dto->userId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    // Старый метод для обратной совместимости (можно удалить позже)
    public function saveUserPrioritiesOld(int $userId, array $data): array
    {
        $dto = SavePrioritiesDTO::fromRequest($data, $userId);
        return $this->saveUserPriorities($dto);
    }

    private function deleteUserPriorities(int $userId): void
    {
        UserDirection::where('user_id', $userId)->delete();
        UserProfile::where('user_id', $userId)->delete();

        Log::info('Deleted old user priorities', ['user_id' => $userId]);
    }

    private function saveDirectionsPriorities(SavePrioritiesDTO $dto): int
    {
        $savedCount = 0;

        foreach ($dto->directions as $directionDTO) {
            UserDirection::create([
                'user_id' => $dto->userId,
                'direction_id' => $directionDTO->id,
                'priority' => $directionDTO->priority
            ]);

            $savedCount++;

            Log::debug("Created user_direction", [
                'user_id' => $dto->userId,
                'direction_id' => $directionDTO->id,
                'priority' => $directionDTO->priority
            ]);
        }

        return $savedCount;
    }

    private function saveProfilesPriorities(SavePrioritiesDTO $dto): int
    {
        $savedCount = 0;

        foreach ($dto->directions as $directionDTO) {
            foreach ($directionDTO->profiles as $profileDTO) {
                UserProfile::create([
                    'user_id' => $dto->userId,
                    'profile_id' => $profileDTO->id,
                    'priority' => $profileDTO->priority,
                    'direction_id' => $directionDTO->id
                ]);

                $savedCount++;

                Log::debug("Created user_profile", [
                    'user_id' => $dto->userId,
                    'profile_id' => $profileDTO->id,
                    'priority' => $profileDTO->priority,
                    'direction_id' => $directionDTO->id
                ]);
            }
        }

        return $savedCount;
    }

    public function getUserPriorities(int $userId)
    {
        return [
            'directions' => UserDirection::with('direction')
                ->where('user_id', $userId)
                ->orderBy('priority')
                ->get(),
            'profiles' => UserProfile::with('profile')
                ->where('user_id', $userId)
                ->orderBy('priority')
                ->get()
        ];
    }

}
