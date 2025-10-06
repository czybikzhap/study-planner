<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePriorityResource extends JsonResource
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
            'direction_id' => $this->direction_id,
            'priority' => $pivot->priority,
            'user_id' => $pivot->user_id,
            'created_at' => $pivot->created_at,
            'updated_at' => $pivot->updated_at,

            'direction' => $this->whenLoaded('direction', function () {
                return [
                    'id' => $this->direction->id,
                    'name' => $this->direction->name,
                    'number' => $this->direction->number,
                ];
            }),
        ];
    }
}
