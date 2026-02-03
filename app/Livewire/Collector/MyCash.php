<?php

namespace App\Livewire\Collector;

use App\Src\Installments\Models\InstallmentModel; // Asegúrate de importar esto
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Livewire\Component;

class MyCash extends Component
{
    public function render()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        // Fechas para el cálculo mensual
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // 1. Pagos de HOY (Caja Diaria)
        $payments = PaymentsModel::where('user_id', $userId)
            ->whereDate('payment_date', $today)
            ->with('installment.credit.client')
            ->orderBy('created_at', 'desc')
            ->get();

        $cash = $payments->where('payment_method.value', 'cash')->sum('amount');
        $transfer = $payments->where('payment_method.value', 'transfer')->sum('amount');

        // 2. Cálculo de Meta MENSUAL (Barra de Progreso)
        // A. Lo que debía cobrar este mes (Cuotas asignadas a él que vencen este mes)
        $expectedMonth = InstallmentModel::whereHas('credit', function ($q) use ($userId) {
            $q->where('collector_id', $userId)
                ->where('status', 'active');
        })
            ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // B. Lo que realmente cobró este mes
        $collectedMonth = PaymentsModel::where('user_id', $userId)
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // C. Porcentaje
        $monthlyGoalPercent = $expectedMonth > 0
            ? ($collectedMonth / $expectedMonth) * 100
            : 0;

        return view('livewire.collector.my-cash', [
            'payments' => $payments,
            'cash' => $cash,
            'transfer' => $transfer,
            'total' => $cash + $transfer,
            'monthlyGoalPercent' => $monthlyGoalPercent, // Variable nueva
        ])->layout('layouts.app');
    }
}
