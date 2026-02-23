<?php

namespace App\Src\Credits\Actions;

use App\Src\Credits\DTOs\CreateCreditData;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;
use Illuminate\Support\Facades\DB;

class CreateCreditAction
{
    public function execute(CreateCreditData $data)
    {
        $teoricalTotalAmount = $data->amountNet * (1 + $data->interestRate / 100);

        $teoricalInstallmentAmount = $teoricalTotalAmount / $data->installmentsCount;

        $roundedInstallmentAmount = ceil($teoricalInstallmentAmount / 1000) * 1000;

        $finalTotalAmount = $roundedInstallmentAmount * $data->installmentsCount;


        return DB::transaction(function () use ($data, $finalTotalAmount, $roundedInstallmentAmount) {

            $credit = CreditsModel::create([
                'client_id' => $data->clientId,
                'collector_id' => $data->collectorId,
                'amount_net' => $data->amountNet,
                'amount_total' => $finalTotalAmount,
                'interest_rate' => $data->interestRate,
                'installments_count' => $data->installmentsCount,
                'payment_frequency' => $data->paymentFrequency,
                'start_date' => $data->startDate,
                'date_of_award' => $data->dateOfAward,
                'status' => 'active',
            ]);

            $this->generateInstallments($credit, $data, $roundedInstallmentAmount);

            return $credit;
        });
    }

    private function generateInstallments(CreditsModel $credit, CreateCreditData $data, float $installmentAmount): void
    {
        $currentDate = $data->startDate->copy();

        for ($i = 1; $i <= $data->installmentsCount; $i++) {
            if ($i > 1) {
                if ($data->paymentFrequency === PaymentFrequencyEnum::DAILY) {
                    $currentDate->addWeekday();
                } else {
                    match ($data->paymentFrequency) {
                        PaymentFrequencyEnum::WEEKLY => $currentDate->addWeek(),
                        PaymentFrequencyEnum::MONTHLY => $currentDate->addMonth(),
                        default => $currentDate->addDay(),
                    };

                    if ($currentDate->isWeekend()) {
                        $currentDate->nextWeekday();
                    }
                }
            } else {
                if ($currentDate->isWeekend()) {
                    $currentDate->nextWeekday();
                }
            }

            $isHistorical = $i < $data->startInstallment;

            $installment = InstallmentModel::create([
                'credit_id' => $credit->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'amount_paid' => $isHistorical ? $installmentAmount : 0,
                'due_date' => $currentDate->copy(),
                'status' => $isHistorical ? InstallmentStatusEnum::PAID : InstallmentStatusEnum::PENDING,
            ]);

            if ($isHistorical) {
                PaymentsModel::create([
                    'installment_id' => $installment->id,
                    'user_id' => $data->historicalCollectorId,
                    'amount' => $installmentAmount,
                    'payment_method' => 'cash',
                    'payment_date' => $currentDate->copy(),
                    'proof_of_payment' => 'MIGRACION_SISTEMA',
                ]);
            }
        }
    }
}
