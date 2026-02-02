<?php

namespace App\Src\Payments\DTOs;

use App\Src\Payments\Enums\PaymentMethodsEnum;
use Carbon\Carbon;

class CreatePaymentData
{
    public function __construct(
        public int $installmentId,
        public int $userId, // Quién cobró (el usuario logueado)
        public float $amount,
        public PaymentMethodsEnum $method,
        public Carbon $paymentDate,
        public ?string $proofOfPayment = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            installmentId: $data['installment_id'],
            userId: auth()->id(),
            amount: (float) $data['amount'],
            method: PaymentMethodsEnum::from($data['payment_method']),
            paymentDate: Carbon::now(),
        );
    }
}
