<?php

namespace App\Livewire\Treasury;

use App\Models\User;
use App\Src\CashOperation\Models\CashOperationModel;
use Livewire\Component;

class Index extends Component
{
    protected $listeners = ['surrenderProcessed' => '$refresh'];

    public function render()
    {
        // 1. Obtener Cobradores
        $collectors = User::where('role', 'collector')->get();

        // 2. Calcular Saldos en Tiempo Real
        // Esto podría optimizarse con SQL raw.
        $collectors->map(function ($collector) {
            $ingresos = CashOperationModel::where('user_id', $collector->id)->where('type', 'income')->sum('amount');
            $egresos = CashOperationModel::where('user_id', $collector->id)->where('type', 'expense')->sum('amount');

            $collector->balance = $ingresos - $egresos; // Propiedad dinámica
            return $collector;
        });

        // 3. Calcular Caja Central (Admin actual)
        $adminId = auth()->id();
        $adminIngresos = CashOperationModel::where('user_id', $adminId)->where('type', 'income')->sum('amount');
        $adminEgresos = CashOperationModel::where('user_id', $adminId)->where('type', 'expense')->sum('amount');
        $centralBalance = $adminIngresos - $adminEgresos;

        // 4. Dinero total en la calle (Suma de lo que tienen los cobradores)
        $streetMoney = $collectors->sum('balance');

        return view('livewire.treasury.index', [
            'collectors' => $collectors,
            'centralBalance' => $centralBalance,
            'streetMoney' => $streetMoney
        ])->layout('layouts.app');
    }
}
