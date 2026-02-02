<?php

namespace App\Livewire\Collector;

use App\Src\Client\Models\ClientModel; // Asegúrate de importar esto
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $collectorId = auth()->id();
        $today = Carbon::today();

        // 1. Resumen de Caja (Lo mantenemos porque es útil)
        $collectedToday = PaymentsModel::where('user_id', $collectorId)
            ->whereDate('payment_date', $today)
            ->sum('amount');

        // 2. Buscamos CLIENTES con cuotas exigibles (Vencidas o de Hoy)
        // Usamos ClientModel como punto de partida para agrupar naturalmente
        $clientsWithDebt = ClientModel::whereHas('credits', function ($q) use ($collectorId, $today) {
            // Filtro 1: Créditos activos de este cobrador
            $q->where('collector_id', $collectorId)
                ->where('status', 'active')
                // Filtro 2: Que tengan cuotas vencidas/hoy y no pagadas
                ->whereHas('installments', function ($qi) use ($today) {
                    $qi->where('due_date', '<=', $today)
                        ->where('status', '!=', 'paid');
                });
        })
            // Aquí cargamos las relaciones FILTRADAS para usar en la vista
            ->with(['credits.installments' => function ($q) use ($today) {
                $q->where('due_date', '<=', $today) // CLAVE: Solo hasta hoy
                    ->where('status', '!=', 'paid')
                    ->orderBy('due_date', 'asc'); // Las más viejas primero
            }])
            ->get();

        // Limpieza de datos:
        // Como un cliente puede tener varios créditos, aplanamos las cuotas en una sola lista por cliente
        $groupedClients = $clientsWithDebt->map(function ($client) {
            // Unimos todas las cuotas de todos sus créditos activos
            $allInstallments = $client->credits->flatMap->installments;

            // Si después de filtrar no queda nada (por seguridad), no lo mostramos
            if ($allInstallments->isEmpty()) return null;

            return [
                'client' => $client,
                'installments' => $allInstallments,
                'total_due' => $allInstallments->sum(fn($i) => $i->amount - $i->amount_paid),
            ];
        })->filter(); // Elimina los nulos

        return view('livewire.collector.dashboard', [
            'collectedToday' => $collectedToday,
            'groupedClients' => $groupedClients,
        ])->layout('layouts.app');
    }
}
