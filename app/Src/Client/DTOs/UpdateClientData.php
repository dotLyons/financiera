<?php

namespace App\Src\Client\DTOs;

class UpdateClientData
{
    public function __construct(
        public string $lastName,
        public string $firstName,
        public string $dni,
        public string $rubro,
        public ?string $phone,
        public ?string $referencePhone,
        public string $address,
        public ?string $secondAddress,
        public ?string $notes,
        public string $status, // Agregamos status para poder suspenderlo
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            lastName: $data['last_name'],
            firstName: $data['first_name'],
            dni: $data['dni'],
            rubro: $data['rubro'],
            phone: $data['phone'] ?? null,
            referencePhone: $data['reference_phone'] ?? null,
            address: $data['address'],
            secondAddress: $data['second_address'] ?? null,
            notes: $data['notes'] ?? null,
            status: $data['status'],
        );
    }
}
