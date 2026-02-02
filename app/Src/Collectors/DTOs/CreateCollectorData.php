<?php

namespace App\Src\Collectors\DTOs;

class CreateCollectorData
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
        );
    }
}
