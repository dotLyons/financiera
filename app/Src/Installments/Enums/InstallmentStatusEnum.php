<?php

namespace App\Src\Installments\Enums;

enum InstallmentStatusEnum: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
}
