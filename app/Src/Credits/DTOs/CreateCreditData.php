<?php

namespace App\Src\Credits\DTOs;

use App\Src\Credits\Enums\PaymentFrequencyEnum;
use Carbon\Carbon;

class CreateCreditData
{
    public function __construct(
        public int $clientId,
        public int $collectorId,
        public float $amountNet,
        public float $interestRate,
        public int $installmentsCount,
        public PaymentFrequencyEnum $paymentFrequency,
        public Carbon $startDate,
        public Carbon $dateOfAward,
        public int $startInstallment = 1,
        public ?int $historicalCollectorId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            clientId: $data['client_id'],
            collectorId: $data['collector_id'],
            amountNet: (float) $data['amount_net'],
            interestRate: (float) $data['interest_rate'],
            installmentsCount: (int) $data['installments_count'],
            paymentFrequency: PaymentFrequencyEnum::from($data['payment_frequency']),
            startDate: Carbon::parse($data['start_date']),
            dateOfAward: isset($data['date_of_award'])
                ? Carbon::parse($data['date_of_award'])
                : Carbon::now(),
            startInstallment: (int) ($data['start_installment'] ?? 1),
            historicalCollectorId: !empty($data['historical_collector_id'])
                ? (int) $data['historical_collector_id']
                : null,
        );
    }
}
