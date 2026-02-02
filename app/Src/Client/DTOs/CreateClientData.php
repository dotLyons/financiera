<?php

namespace App\Src\Client\DTOs;

class CreateClientData
{
    public function __construct(
        public string $lastName,
        public string $firstName,
        public string $dni,
        public ?string $phone, // ? permite nulos
        public string $address,
        public ?string $notes,
        public int $createdBy,
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
            createdBy: auth()->id(),
        );
    }
}
