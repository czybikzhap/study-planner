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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'direction_id' => $this->direction_id,
            'priority' => $this->pivot->priority ?? $this->priority,
            'user_id' => $this->pivot->user_id ?? null,
            'created_at' => $this->pivot->created_at ?? null,
            'updated_at' => $this->pivot->updated_at ?? null,

            // Информация о направлении, если нужно
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
