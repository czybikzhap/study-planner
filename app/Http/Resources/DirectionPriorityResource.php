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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'priority' => $this->saved_priority ?? $this->getAttribute('saved_priority'),
            'user_id' => $this->saved_user_id ?? $this->getAttribute('saved_user_id'),
            'created_at' => $this->saved_created_at ?? $this->getAttribute('saved_created_at'),
            'updated_at' => $this->saved_updated_at ?? $this->getAttribute('saved_updated_at'),

            'profiles' => $this->when($this->profiles, function () {
                return $this->profiles->map(function ($profile) {
                    return [
                        'id' => $profile->id,
                        'name' => $profile->name,
                        'priority' => $profile->saved_priority ?? $profile->getAttribute('saved_priority'),
                        'user_id' => $profile->saved_user_id ?? $profile->getAttribute('saved_user_id'),
                    ];
                });
            }, []),
        ];
    }
}
