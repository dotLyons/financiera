<?php

namespace App\Src\Client\DTOs;

class UpdateClientData
{
    public function __construct(
        public string $lastName,
        public string $firstName,
        public string $dni,
        public ?string $phone,
        public string $address,
        public ?string $notes,
        public string $status, // Agregamos status para poder suspenderlo
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            lastName: $data['last_name'],
            firstName: $data['first_name'],
            dni: $data['dni'],
            phone: $data['phone'] ?? null,
            address: $data['address'],
            notes: $data['notes'] ?? null,
            status: $data['status'],
        );
    }
}
