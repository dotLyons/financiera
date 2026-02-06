<?php

namespace App\Src\Credits\Actions;

use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use App\Src\Installments\Models\InstallmentModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RefinanceCreditAction
{
    public function execute(
        CreditsModel $credit,
        int $newInstallmentsCount,
        string $newFrequency,
        float $newInterestRate,
        Carbon $startDate
    ): void {
        DB::transaction(function () use ($credit, $newInstallmentsCount, $newFrequency, $newInterestRate, $startDate) {

            $pendingInstallments = $credit->installments->where('status', '!=', InstallmentStatusEnum::PAID->value);

            $refinanceCapital = 0;

            foreach ($pendingInstallments as $installment) {
                $debt = $installment->amount - $installment->amount_paid;
                $refinanceCapital += $debt;
            }

            if ($refinanceCapital <= 0) {
                throw new \Exception("No hay saldo pendiente para refinanciar.");
            }

            $newTotalDebt = $refinanceCapital * (1 + ($newInterestRate / 100));
            $newAmountPerInstallment = $newTotalDebt / $newInstallmentsCount;

            foreach ($pendingInstallments as $installment) {
                if ($installment->amount_paid > 0) {
                    $installment->amount = $installment->amount_paid;
                    $installment->status = InstallmentStatusEnum::PAID->value;
                    $installment->save();
                } else {
                    $installment->forceDelete();
                }
            }

            $lastInstallmentNumber = $credit->installments()->max('installment_number') ?? 0;

            for ($i = 1; $i <= $newInstallmentsCount; $i++) {

                if ($newFrequency === 'daily') {
                    $dueDate = $startDate->copy()->addWeekdays($i);
                } else {
                    $dueDate = match ($newFrequency) {
                        'weekly' => $startDate->copy()->addWeeks($i),
                        'biweekly' => $startDate->copy()->addWeeks($i * 2),
                        'monthly' => $startDate->copy()->addMonths($i),
                        default => $startDate->copy()->addDays($i),
                    };

                    if ($dueDate->isWeekend()) {
                        $dueDate->nextWeekday();
                    }
                }

                InstallmentModel::create([
                    'credit_id' => $credit->id,
                    'installment_number' => $lastInstallmentNumber + $i,
                    'amount' => $newAmountPerInstallment,
                    'amount_paid' => 0,
                    'due_date' => $dueDate,
                    'status' => InstallmentStatusEnum::PENDING->value,
                ]);
            }

            $totalPaidHistorically = $credit->installments()->where('status', 'paid')->sum('amount_paid');

            $credit->amount_total = $totalPaidHistorically + $newTotalDebt;
            $credit->installments_count = $lastInstallmentNumber + $newInstallmentsCount;
            $credit->payment_frequency = $newFrequency;

            $credit->is_refinanced = true;

            $credit->save();
        });
    }
}
