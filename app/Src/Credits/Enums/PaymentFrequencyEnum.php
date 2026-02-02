<?php

namespace App\Src\Credits\Enums;

enum PaymentFrequencyEnum: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public function label(): string
    {
        return match($this) {
            self::DAILY => 'Diario',
            self::WEEKLY => 'Semanal',
            self::MONTHLY => 'Mensual',
        };
    }
}
