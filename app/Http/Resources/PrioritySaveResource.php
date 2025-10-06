<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Direction;
use App\Models\Profile;

class PrioritySaveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $this['user_id'];

        //print_r( $this->getProfileDetails($userId));die;

        return [
            'success' => true,
            'user_id' => $userId,
            'timestamp' => now()->toDateTimeString(),
            'summary' => [
                'total_directions' => $this['saved_directions'],
                'total_profiles' => $this['saved_profiles'],
            ],

            'directions' => DirectionPriorityResource::collection(
                $this->getDirectionDetails($userId)
            ),

            'profiles' => ProfilePriorityResource::collection(
                $this->getProfileDetails($userId)
            ),
        ];
    }

    private function getDirectionDetails(int $userId)
    {
        return Direction::with(['profiles' => function ($q) use ($userId) {
            $q->select('profiles.*', 'user_profiles.priority as saved_priority')
                ->join('user_profiles', 'profiles.id', '=', 'user_profiles.profile_id')
                ->where('user_profiles.user_id', $userId);
        }])
            ->select('directions.*', 'user_directions.priority as saved_priority')
            ->join('user_directions', 'directions.id', '=', 'user_directions.direction_id')
            ->where('user_directions.user_id', $userId)
            ->get();
    }

    private function getProfileDetails(int $userId)
    {
        return Profile::select('profiles.*', 'user_profiles.priority as saved_priority')
            ->join('user_profiles', 'profiles.id', '=', 'user_profiles.profile_id')
            ->where('user_profiles.user_id', $userId)
            ->with('direction')
            ->get();
    }
}

