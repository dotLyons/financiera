<?php

namespace App\Src\Client\Enum;

enum ClientStatusEnum: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            ClientStatusEnum::ACTIVE => 'Active',
            ClientStatusEnum::SUSPENDED => 'Suspended',
        };
    }
}
