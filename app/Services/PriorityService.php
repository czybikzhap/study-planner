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

    public function saveUserPriorities(SavePrioritiesDTO $dto): array
    {
        DB::beginTransaction();

        try {
            $this->deleteUserPriorities($dto->userId);

            $savedDirectionsCount = $this->saveDirectionsPriorities($dto);
            $savedProfilesCount = $this->saveProfilesPriorities($dto);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'user_id' => $dto->userId,
            'saved_directions' => $savedDirectionsCount,
            'saved_profiles' => $savedProfilesCount,
        ];
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


}
