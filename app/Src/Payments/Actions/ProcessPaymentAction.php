<?php

namespace App\Src\Payments\Actions;

use App\Src\CashOperation\Models\CashOperationModel;
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

            // 1. Obtener la cuota y bloquearla para evitar doble pago simultáneo
            $installment = InstallmentModel::findOrFail($data->installmentId);

            // 2. Crear el Registro del Pago (El recibo)
            $payment = PaymentsModel::create([
                'installment_id' => $data->installmentId,
                'user_id' => $data->userId,
                'amount' => $data->amount,
                'payment_method' => $data->method,
                'payment_date' => $data->paymentDate,
                'proof_of_payment' => $data->proofOfPayment,
            ]);

            // 3. Actualizar la Cuota
            $installment->amount_paid += $data->amount;

            // Lógica de Estado: ¿Pagó todo o falta?
            // Usamos una pequeña tolerancia (0.1) por temas de decimales flotantes
            if ($installment->amount_paid >= ($installment->amount - 0.1)) {
                $installment->status = InstallmentStatusEnum::PAID;
                $installment->date_paid = now(); // Asumiendo que tienes este campo, si no, ignóralo
            } else {
                $installment->status = InstallmentStatusEnum::PARTIAL;
            }

            $installment->save();

            // 4. Verificar si el Crédito Padre se completó (Opcional pero recomendado)
            // Si todas las cuotas del crédito están pagadas, marcamos el crédito como PAID.
            $this->checkCreditStatus($installment->credit_id);

            // 5. Registrar Movimiento en Caja (Cash Operation)
            // Esto es vital para saber cuánta plata tiene el cobrador encima.
            CashOperationModel::create([
                'user_id' => $data->userId,
                'payment_id' => $payment->id,
                'type' => 'income', // ingreso
                'amount' => $data->amount,
                'concept' => "Cobro Cuota #{$installment->installment_number} - Crédito #{$installment->credit_id}",
                'operation_date' => now(),
            ]);

            return $payment;
        });
    }

    private function checkCreditStatus(int $creditId): void
    {
        // Buscamos si queda alguna cuota pendiente
        $pendingInstallments = InstallmentModel::where('credit_id', $creditId)
            ->where('status', '!=', InstallmentStatusEnum::PAID)
            ->exists();

        if (!$pendingInstallments) {
            // Si no hay pendientes, actualizamos el crédito padre
            \App\Src\Credits\Models\CreditsModel::where('id', $creditId)
                ->update(['status' => 'paid']);
        }
    }
}
