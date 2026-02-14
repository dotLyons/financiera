<?php

namespace App\Src\Credits\Actions;

use App\Src\Credits\DTOs\CreateCreditData;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use App\Src\Installments\Models\InstallmentModel;
use Illuminate\Support\Facades\DB;
use ValidationException;

class UpdateCreditAction
{
    public function execute(CreditsModel $credit, CreateCreditData $data, string $reason)
    {
        $hasPayments = $credit->installments()->where('status', InstallmentStatusEnum::PAID)->exists();

        if ($hasPayments) {
            throw new \Exception('No se puede editar un crédito que ya tiene cuotas pagadas. Utilice la Refinanciación.');
        }

        return DB::transaction(function () use ($credit, $data, $reason) {
            $totalAmount = $data->amountNet * (1 + $data->interestRate / 100);

            $credit->update([
                'client_id' => $data->clientId,
                'collector_id' => $data->collectorId,
                'amount_net' => $data->amountNet,
                'amount_total' => $totalAmount,
                'interest_rate' => $data->interestRate,
                'installments_count' => $data->installmentsCount,
                'payment_frequency' => $data->paymentFrequency,
                'start_date' => $data->startDate,
                'date_of_award' => $data->dateOfAward,

                'is_edited' => true,
                'edited_at' => now(),
                'edited_reason' => $reason,
            ]);

            $credit->installments()->delete();

            $this->generateInstallments($credit, $data, $totalAmount);

            return $credit;
        });
    }

    private function generateInstallments(CreditsModel $credit, CreateCreditData $data, float $totalAmount): void
    {
        $installmentAmount = $totalAmount / $data->installmentsCount;
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

            InstallmentModel::create([
                'credit_id' => $credit->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'amount_paid' => 0,
                'due_date' => $currentDate->copy(),
                'status' => InstallmentStatusEnum::PENDING,
            ]);
        }
    }
}
