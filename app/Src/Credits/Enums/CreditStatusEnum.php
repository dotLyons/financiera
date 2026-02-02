<?php

namespace App\Src\Credits\Enums;

enum CreditStatusEnum: string
{
    case ACTIVE = 'active';
    case PAID = 'paid';
    case DEFAULTED = 'defaulted';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Activo',
            self::PAID => 'Pagado',
            self::DEFAULTED => 'Moroso',
        };
    }
}
