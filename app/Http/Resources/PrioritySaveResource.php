<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrioritySaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this['success'] ?? true,
            'saved_directions' => $this['saved_directions'] ?? 0,
            'saved_profiles' => $this['saved_profiles'] ?? 0,
            'user_id' => $this['user_id'] ?? null, // Исправлено
            'timestamp' => $this['timestamp'] ?? now()->toDateTimeString(),

            // Детальная информация о сохраненных направлениях
            'directions' => DirectionPriorityResource::collection(
                $this['saved_direction_details'] ?? []
            ),

            // Детальная информация о сохраненных профилях
            'profiles' => ProfilePriorityResource::collection(
                $this['saved_profile_details'] ?? []
            ),

            // Статистика
            'summary' => [
                'total_directions' => $this['saved_directions'] ?? 0,
                'total_profiles' => $this['saved_profiles'] ?? 0,
                'processing_time' => $this['processing_time'] ?? null,
            ]
        ];
    }
}
