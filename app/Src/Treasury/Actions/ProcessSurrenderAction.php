<?php

namespace App\Src\Treasury\Actions;

use App\Models\User;
use App\Src\CashOperation\Models\CashOperationModel;
use App\Src\Treasury\DTOs\SurrenderData;
use Illuminate\Support\Facades\DB;

class ProcessSurrenderAction
{
    public function execute(SurrenderData $data): void
    {
        DB::transaction(function () use ($data) {
            $collector = User::find($data->collectorId);
            $admin = User::find($data->adminId);

            // 1. EGRESO para el Cobrador (Se saca el dinero de encima)
            CashOperationModel::create([
                'user_id' => $data->collectorId,
                'type' => 'expense',
                'amount' => $data->amount,
                'concept' => "Rendición de caja a {$admin->name}",
                'operation_date' => now(),
            ]);

            // 2. INGRESO para el Admin (Entra a caja central)
            CashOperationModel::create([
                'user_id' => $data->adminId,
                'type' => 'income',
                'amount' => $data->amount,
                'concept' => "Recepción rendición de {$collector->name} - {$data->notes}",
                'operation_date' => now(),
            ]);
        });
    }
}
