<?php

namespace App\Src\CashOperation\Actions;

use App\Models\User;
use App\Src\CashOperation\Models\CashOperationModel;
use App\Src\CashOperation\Models\CashSurrenderModel;
use Illuminate\Support\Facades\DB;

class SurrenderCashAction
{
    public function execute(int $collectorId, int $adminId, float $amount, string $notes = null): CashSurrenderModel
    {
        return DB::transaction(function () use ($collectorId, $adminId, $amount, $notes) {

            $collector = User::findOrFail($collectorId);

            if ($collector->wallet_balance < $amount) {
                throw new \Exception('Fondos insuficientes en la billetera del cobrador.');
            }

            $surrender = CashSurrenderModel::create([
                'collector_id' => $collectorId,
                'admin_id' => $adminId,
                'amount' => $amount,
                'payment_method' => 'cash', // Generalmente rinden efectivo
                'notes' => $notes,
                'surrendered_at' => now(),
            ]);

            $collector->wallet_balance -= $amount;
            $collector->save();

            CashOperationModel::create([
                'user_id' => $collectorId,
                'type' => 'expense', // Salida de dinero
                'amount' => $amount,
                'concept' => "Rendición de dinero a Administración (Recibo #{$surrender->id})",
                'operation_date' => now(),
            ]);

            return $surrender;
        });
    }
}
