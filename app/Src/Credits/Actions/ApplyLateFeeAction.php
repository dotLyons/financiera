<?php

namespace App\Src\Credits\Actions;

use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplyLateFeeAction
{
    // Porcentaje de recargo (5%)
    const PENALTY_RATE = 0.05;

    // Días de gracia antes de aplicar interés
    const GRACE_PERIOD_DAYS = 7;

    public function execute(): void
    {
        // 1. Buscamos créditos ACTIVOS
        $credits = CreditsModel::where('status', 'active')
            ->where(function ($query) {
                // Que nunca hayan tenido penalización O que la última haya sido hace más de 7 días
                $query->whereNull('last_penalty_applied_at')
                    ->orWhere('last_penalty_applied_at', '<=', Carbon::now()->subDays(7));
            })
            ->get();

        foreach ($credits as $credit) {
            $this->processCredit($credit);
        }
    }

    private function processCredit(CreditsModel $credit)
    {
        // 2. Verificar si tiene ALGUNA cuota vencida hace más de 7 días
        $hasLateInstallments = $credit->installments()
            ->where('status', '!=', InstallmentStatusEnum::PAID->value) // Que no esté pagada
            ->where('due_date', '<=', Carbon::now()->subDays(self::GRACE_PERIOD_DAYS)) // Vencida hace 7 dias
            ->exists();

        // Si no hay cuotas viejas impagas, no hacemos nada (aunque sea lunes y deba la del viernes pasado, esperamos al viernes que viene)
        if (!$hasLateInstallments) {
            return;
        }

        DB::transaction(function () use ($credit) {
            // 3. Calcular Saldo Pendiente (Capital Restante)
            // Total prestado + intereses originales - Lo que ya pagó
            $totalDebt = $credit->amount_total; // Ojo: ¿Es sobre el neto o el total con interés? Usualmente es sobre saldo deuda total.
            $totalPaid = $credit->installments()->sum('amount_paid');

            $outstandingBalance = $totalDebt - $totalPaid;

            if ($outstandingBalance <= 0) return;

            // 4. Calcular el 5% de castigo
            $penaltyAmount = $outstandingBalance * self::PENALTY_RATE;

            // 5. Buscar TODAS las cuotas pendientes (Vencidas y Futuras) para repartir el golpe
            $pendingInstallments = $credit->installments()
                ->where('status', '!=', InstallmentStatusEnum::PAID->value)
                ->get();

            if ($pendingInstallments->isEmpty()) return;

            // 6. Dividir el castigo entre las cuotas restantes
            $penaltyPerInstallment = $penaltyAmount / $pendingInstallments->count();

            foreach ($pendingInstallments as $installment) {
                // Aumentamos el monto de la cuota
                $installment->amount += $penaltyPerInstallment;
                // Registramos cuánto es interés punitorio
                $installment->penalty_amount += $penaltyPerInstallment;
                $installment->save();
            }

            // 7. Actualizar el Crédito (Total deuda aumenta) y Marca de tiempo
            $credit->amount_total += $penaltyAmount;
            $credit->last_penalty_applied_at = Carbon::now();
            $credit->save();

            Log::info("Interés aplicado al crédito #{$credit->id}. Saldo: $outstandingBalance. Multa: $penaltyAmount");
        });
    }
}
