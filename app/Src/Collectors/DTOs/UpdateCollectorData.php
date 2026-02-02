<?php

namespace App\Src\Collectors\DTOs;

class UpdateCollectorData
{
    public function __construct(
        public string $name,
        public string $email,
        public bool $isActive,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            isActive: (bool) $data['is_active'],
        );
    }
}
