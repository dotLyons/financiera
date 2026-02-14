<?php

namespace App\Src\Installments\Enums;

enum InstallmentStatusEnum: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PARTIAL => 'Pago Parcial',
            self::PAID => 'Pagada',
            self::OVERDUE => 'Vencida',
        };
    }
}
