<?php

namespace App\DTO;

class SavePrioritiesDTO
{
    /**
     * @param DirectionPriorityDTO[] $directions
     */
    public function __construct(
        public int $userId,
        public array $directions
    ) {}

    // Статический конструктор из Request
    public static function fromRequest(array $validatedData, int $userId): self
    {
        $directions = array_map(function ($directionData) {
            return DirectionPriorityDTO::fromArray($directionData);
        }, $validatedData['directions']);

        return new self(
            userId: $userId,
            directions: $directions
        );
    }

    // Обычный конструктор
    public static function make(int $userId, array $directions): self
    {
        return new self(
            userId: $userId,
            directions: $directions
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'directions' => array_map(fn($direction) => $direction->toArray(), $this->directions),
        ];
    }

    /**
     * @return int[]
     */
    public function getDirectionIds(): array
    {
        return array_map(fn($direction) => $direction->id, $this->directions);
    }

    public function getTotalProfilesCount(): int
    {
        return array_reduce($this->directions,
            fn($count, $direction) => $count + count($direction->profiles), 0);
    }
}
