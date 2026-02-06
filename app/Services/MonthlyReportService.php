<?php

namespace App\Services;

use App\Models\User;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MonthlyReportService
{
    public function getStats(int $month, int $year): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. FLUJO DE CAJA (Dinero Real)
        // Entradas: Todo lo cobrado en el mes
        $payments = PaymentsModel::whereBetween('payment_date', [$startDate, $endDate])->get();
        $inflowCash = $payments->where('payment_method.value', 'cash')->sum('amount');
        $inflowTransfer = $payments->where('payment_method.value', 'transfer')->sum('amount');
        $totalInflow = $inflowCash + $inflowTransfer;

        // Salidas: Créditos otorgados (Capital Neto entregado)
        $newCredits = CreditsModel::whereBetween('start_date', [$startDate, $endDate])->get();
        $totalOutflow = $newCredits->sum('amount_net');

        // Resultado Operativo (Caja)
        $netResult = $totalInflow - $totalOutflow;

        // 2. OPERATIVA
        $creditsCount = $newCredits->count();
        $refinancedCount = $newCredits->where('is_refinanced', true)->count();
        $newClientsCount = $newCredits->unique('client_id')->count(); // Clientes atendidos con créditos nuevos

        // 3. RENDIMIENTO DE COBRADORES
        $collectors = User::where('role', 'collector')->get()->map(function ($collector) use ($startDate, $endDate) {

            // Lo que cobró este mes
            $collected = PaymentsModel::where('user_id', $collector->id)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount');

            // Sus créditos asignados activos
            $assignedCredits = CreditsModel::where('collector_id', $collector->id)
                ->where('status', 'active')
                ->count();

            return [
                'name' => $collector->name,
                'collected' => $collected,
                'credits_managed' => $assignedCredits,
                // Calculamos un % de contribución al total de la empresa
                'contribution_percent' => 0 // Lo calculamos abajo
            ];
        });

        // Calcular porcentajes relativos
        $totalCollectedByAll = $collectors->sum('collected');
        $collectors = $collectors->map(function ($c) use ($totalCollectedByAll) {
            $c['contribution_percent'] = $totalCollectedByAll > 0 ? ($c['collected'] / $totalCollectedByAll) * 100 : 0;
            return $c;
        })->sortByDesc('collected'); // Ordenar del mejor al peor

        return [
            'period' => $startDate->locale('es')->translatedFormat('F Y'),
            'month' => $month,
            'year' => $year,
            'financial' => [
                'inflow_total' => $totalInflow,
                'inflow_cash' => $inflowCash,
                'inflow_transfer' => $inflowTransfer,
                'outflow_credits' => $totalOutflow,
                'net_result' => $netResult,
            ],
            'operational' => [
                'credits_count' => $creditsCount,
                'refinanced_count' => $refinancedCount,
                'average_credit' => $creditsCount > 0 ? $totalOutflow / $creditsCount : 0,
            ],
            'collectors' => $collectors,
        ];
    }
}
