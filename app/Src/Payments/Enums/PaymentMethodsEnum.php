<?php

namespace App\Src\Payments\Enums;

enum PaymentMethodsEnum: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    //case DEBIT_CARD = 'debit_card';
    //case CREDIT_CARD = 'credit_card';
    //case MP = 'mercadopago';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'Efectivo',
            self::TRANSFER => 'Transferencia Bancaria',
        //  self::DEBIT_CARD => 'Tarjeta de Débito',
        //  self::CREDIT_CARD => 'Tarjeta de Crédito',
        //  self::MP => 'Mercado Pago',
        };
    }
}
