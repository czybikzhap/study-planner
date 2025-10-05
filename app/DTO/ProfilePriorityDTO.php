<?php

namespace App\DTO;

class ProfilePriorityDTO
{
    public function __construct(
        public int $id,
        public int $priority
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            priority: $data['priority']
        );
    }

    public static function make(int $id, int $priority): self
    {
        return new self(
            id: $id,
            priority: $priority
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'priority' => $this->priority,
        ];
    }
}
