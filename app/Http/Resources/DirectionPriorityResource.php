<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DirectionPriorityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pivot = optional($this->users->first()?->pivot);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'priority' => $pivot->priority,
            'user_id' => $pivot->user_id,
            'created_at' => $pivot->created_at,
            'updated_at' => $pivot->updated_at,

            // Профили внутри направления
            'profiles' => $this->whenLoaded('profiles', function () {
                return $this->profiles->map(function ($profile) {
                    $pivot = optional($profile->users->first()?->pivot);

                    return [
                        'id' => $profile->id,
                        'name' => $profile->name,
                        'priority' => $pivot->priority,
                        'user_id' => $pivot->user_id,
                        'created_at' => $pivot->created_at,
                        'updated_at' => $pivot->updated_at,
                    ];
                });
            }),
        ];
    }
}
