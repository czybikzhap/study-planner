<?php

namespace App\Services;

use App\DTO\SavePrioritiesDTO;
use App\Models\Direction;
use App\Models\Profile;
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

            $savedDirectionsCount = $this->saveDirectionsPriorities($dto);
            $savedProfilesCount = $this->saveProfilesPriorities($dto);

            $directionDetails = $this->getSavedDirectionDetails($dto);
            $profileDetails = $this->getSavedProfileDetails($dto);

            DB::commit();

            return [
                'success' => true,
                'saved_directions' => $savedDirectionsCount,
                'saved_profiles' => $savedProfilesCount,
                'user_id' => $dto->userId,
                'timestamp' => now()->toDateTimeString(),
                'saved_direction_details' => $directionDetails,
                'saved_profile_details' => $profileDetails,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function getSavedDirectionDetails(SavePrioritiesDTO $dto): array
    {
        $directionsArray = json_decode(json_encode($dto->directions), true);

        $details = [];
        foreach ($directionsArray as $directionData) {
            $direction = Direction::with('profiles')->find($directionData['id']);
            if ($direction) {
                $direction->setAttribute('saved_priority', $directionData['priority']);
                $direction->setAttribute('saved_user_id', $dto->userId);
                $direction->setAttribute('saved_created_at', now());
                $direction->setAttribute('saved_updated_at', now());

                if ($direction->profiles) {
                    foreach ($direction->profiles as $profile) {
                        $profilePriority = $this->findProfilePriority($directionData['profiles'], $profile->id);
                        $profile->setAttribute('saved_priority', $profilePriority);
                        $profile->setAttribute('saved_user_id', $dto->userId);
                    }
                }

                $details[] = $direction;
            }
        }
        return $details;
    }

    private function getSavedProfileDetails(SavePrioritiesDTO $dto): array
    {
        $directionsArray = json_decode(json_encode($dto->directions), true);

        $details = [];
        foreach ($directionsArray as $directionData) {
            foreach ($directionData['profiles'] as $profileData) {
                $profile = Profile::with('direction')->find($profileData['id']);
                if ($profile) {

                    $profile->setAttribute('saved_priority', $profileData['priority']);
                    $profile->setAttribute('saved_user_id', $dto->userId);
                    $profile->setAttribute('saved_created_at', now());
                    $profile->setAttribute('saved_updated_at', now());

                    $details[] = $profile;
                }
            }
        }
        return $details;
    }

    /**
     * Находит приоритет профиля в массиве отправленных данных
     */
    private function findProfilePriority(array $profiles, int $profileId): ?int
    {
        foreach ($profiles as $profile) {
            if ($profile['id'] == $profileId) {
                return $profile['priority'];
            }
        }
        return null;
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
