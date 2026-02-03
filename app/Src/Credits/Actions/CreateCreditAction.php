<?php

namespace App\Src\Credits\Actions;

use App\Src\Credits\DTOs\CreateCreditData;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use App\Src\Installments\Models\InstallmentModel;
use Illuminate\Support\Facades\DB;

class CreateCreditAction
{
    public function execute(CreateCreditData $data)
    {
        $totalAmount = $data->amountNet * (1 + $data->interestRate / 100);

        $totalAmount = round($totalAmount, 2);

        $installmentAmount = $totalAmount / $data->installmentsCount;
        $installmentAmount = round($installmentAmount, 2);

        return DB::transaction(function () use ($data, $totalAmount, $installmentAmount) {
            $credit = CreditsModel::create([
                'client_id' => $data->clientId,
                'collector_id' => $data->collectorId,
                'amount_net' => $data->amountNet,
                'amount_total' => $totalAmount,
                'interest_rate' => $data->interestRate,
                'installments_count' => $data->installmentsCount,
                'payment_frequency' => $data->paymentFrequency,
                'start_date' => $data->startDate,
                'status' => 'active',
            ]);

            $this->generateInstallments($credit, $data, $installmentAmount);

            return $credit;
        });
    }

    private function generateInstallments(CreditsModel $credit, CreateCreditData $data, float $amount): void
    {
        $dueDate = $data->startDate->copy();

        for ($i = 1 ; $i <= $data->installmentsCount ; $i++) {
            match ($data->paymentFrequency) {
                PaymentFrequencyEnum::DAILY => $dueDate->addDay(),
                PaymentFrequencyEnum::WEEKLY => $dueDate->addWeek(),
                PaymentFrequencyEnum::MONTHLY => $dueDate->addMonth(),
            };

            InstallmentModel::create([
                'credit_id' => $credit->id,
                'installment_number' => $i,
                'amount' => $amount,
                'amount_paid' => 0,
                'due_date' => $dueDate->copy(),
                'status' => InstallmentStatusEnum::PENDING,
            ]);
        }
    }
}
