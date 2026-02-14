<?php

namespace App\Src\Payments\Actions;

use App\Models\User;
use App\Src\CashOperation\Models\CashOperationModel;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\DTOs\CreatePaymentData;
use App\Src\Payments\Models\PaymentsModel;
use Illuminate\Support\Facades\DB;

class ProcessPaymentAction
{
    public function execute(CreatePaymentData $data): PaymentsModel
    {
        return DB::transaction(function () use ($data) {

            $installment = InstallmentModel::findOrFail($data->installmentId);

            $payment = PaymentsModel::create([
                'installment_id' => $data->installmentId,
                'user_id' => $data->userId,
                'amount' => $data->amount,
                'payment_method' => $data->method,
                'payment_date' => $data->paymentDate,
                'proof_of_payment' => $data->proofOfPayment,
            ]);

            $totalCuota = (float) $installment->amount;
            $pagadoPreviamente = (float) $installment->amount_paid;
            $pagoActual = (float) $data->amount;

            $nuevoTotalPagado = $pagadoPreviamente + $pagoActual;

            if ($nuevoTotalPagado >= ($totalCuota - 0.01)) {
                $installment->status = InstallmentStatusEnum::PAID;
                $installment->amount_paid = $totalCuota;
            } else {
                $installment->status = InstallmentStatusEnum::PARTIAL;
                $installment->amount_paid = $nuevoTotalPagado;
            }

            $installment->save();

            $this->checkCreditStatus($installment->credit_id);

            CashOperationModel::create([
                'user_id' => $data->userId,
                'payment_id' => $payment->id,
                'type' => 'income',
                'amount' => $data->amount,
                'concept' => "Cobro Cuota #{$installment->installment_number} - Crédito #{$installment->credit_id}",
                'operation_date' => now(),
            ]);

            $collector = User::find($data->userId);
            if ($collector) {
                $collector->wallet_balance += $data->amount;
                $collector->save();
            }

            return $payment;
        });
    }

    private function checkCreditStatus(int $creditId): void
    {
        $hasPendingInstallments = InstallmentModel::where('credit_id', $creditId)
            ->where('status', '!=', InstallmentStatusEnum::PAID)
            ->exists();

        if (!$hasPendingInstallments) {
            CreditsModel::where('id', $creditId)
                ->update(['status' => 'paid']); 
        }
    }
}
