<?php

namespace App\Src\Payments\Enums;

enum PaymentMethodsEnum: string
{
    //case CREDIT_CARD = 'credit_card';
    //case DEBIT_CARD = 'debit_card';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
}
