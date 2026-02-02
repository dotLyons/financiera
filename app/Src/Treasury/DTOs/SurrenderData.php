<?php

namespace App\Src\Treasury\DTOs;

class SurrenderData
{
    public function __construct(
        public int $collectorId,
        public int $adminId,
        public float $amount,
        public string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            collectorId: $data['collector_id'],
            adminId: auth()->id(),
            amount: (float) $data['amount'],
            notes: $data['notes'] ?? '',
        );
    }
}
