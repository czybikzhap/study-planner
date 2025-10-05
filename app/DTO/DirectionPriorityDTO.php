<?php

namespace App\DTO;

class DirectionPriorityDTO
{
    /**
     * @param ProfilePriorityDTO[] $profiles
     */
    public function __construct(
        public int $id,
        public int $priority,
        public array $profiles
    ) {}

    public static function fromArray(array $data): self
    {
        $profiles = array_map(function ($profileData) {
            return ProfilePriorityDTO::fromArray($profileData);
        }, $data['profiles']);

        return new self(
            id: $data['id'],
            priority: $data['priority'],
            profiles: $profiles
        );
    }

    public static function make(int $id, int $priority, array $profiles): self
    {
        return new self(
            id: $id,
            priority: $priority,
            profiles: $profiles
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'priority' => $this->priority,
            'profiles' => array_map(fn($profile) => $profile->toArray(), $this->profiles),
        ];
    }

    /**
     * @return int[]
     */
    public function getProfileIds(): array
    {
        return array_map(fn($profile) => $profile->id, $this->profiles);
    }
}
