<?php

namespace App\Src\CashOperation\Enums;

enum CashOperationTypeEnum: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
}
