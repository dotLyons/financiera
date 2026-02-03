<?php

namespace App\Livewire\Treasury;

use App\Models\User;
use App\Src\CashOperation\Models\CashOperationModel;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    protected $listeners = ['surrenderProcessed' => '$refresh'];

    public function render()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // 1. Obtenemos cobradores y calculamos sus métricas en memoria
        $collectors = User::where('role', 'collector')
            ->where('is_active', true)
            ->get()
            ->map(function ($collector) use ($today, $startOfMonth, $endOfMonth) {

                // A. Caja Diaria (Efectivo vs Transferencia HOY)
                $todaysPayments = PaymentsModel::where('user_id', $collector->id)
                    ->whereDate('payment_date', $today)
                    ->get();

                $collector->cash_in_hand = $todaysPayments->where('payment_method.value', 'cash')->sum('amount');
                $collector->transfers_in_hand = $todaysPayments->where('payment_method.value', 'transfer')->sum('amount');
                $collector->total_today = $collector->cash_in_hand + $collector->transfers_in_hand;

                // B. Rendimiento Mensual (Para la barra de progreso)
                // -----------------------------------------------------

                // Meta: Suma de cuotas asignadas a este cobrador que vencen ESTE MES
                $expectedMonth = InstallmentModel::whereHas('credit', function ($q) use ($collector) {
                    $q->where('collector_id', $collector->id)
                        ->where('status', 'active');
                })
                    ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                // Realidad: Suma de pagos recibidos por este cobrador ESTE MES
                $collectedMonth = PaymentsModel::where('user_id', $collector->id)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                // Cálculo del porcentaje (evitando división por cero)
                $collector->monthly_goal_percent = $expectedMonth > 0
                    ? ($collectedMonth / $expectedMonth) * 100
                    : 0;

                return $collector;
            });

        // Totales Generales para los KPIs superiores
        $totalCash = $collectors->sum('cash_in_hand');
        $totalTransfer = $collectors->sum('transfers_in_hand');

        return view('livewire.treasury.index', [
            'collectors' => $collectors,
            'totalCash' => $totalCash,
            'totalTransfer' => $totalTransfer,
            'grandTotal' => $totalCash + $totalTransfer,
        ])->layout('layouts.app');
    }
}
