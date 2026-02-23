<?php

namespace App\Livewire\Collector;

use App\Src\Client\Models\ClientModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $collectorId = auth()->id();
        $today = Carbon::today();

        // 1. Resumen de Caja Diaria
        $collectedToday = PaymentsModel::where('user_id', $collectorId)
            ->whereDate('payment_date', $today)
            ->sum('amount');

        // 2. Buscamos TODOS los clientes con créditos ACTIVOS de este cobrador
        // Ya no filtramos por "cuotas vencidas" en la consulta base
        $clients = ClientModel::whereHas('credits', function ($q) use ($collectorId) {
            $q->where('collector_id', $collectorId)
                ->where('status', 'active');
        })
            ->with(['credits' => function ($q) use ($collectorId) {
                $q->where('collector_id', $collectorId)
                    ->where('status', 'active')
                    ->with(['installments' => function ($qi) {
                        // Traemos TODAS las cuotas pendientes, sin importar la fecha
                        $qi->where('status', '!=', 'paid')
                            ->orderBy('due_date', 'asc');
                    }]);
            }])
            ->get();

        // 3. Procesamos y clasificamos a los clientes (Con Deuda vs Al Día)
        $groupedClients = $clients->map(function ($client) use ($today) {

            // Unimos todas las cuotas pendientes de sus créditos activos y las ordenamos
            $allUnpaidInstallments = $client->credits->flatMap->installments->sortBy('due_date')->values();

            // Si no tiene cuotas pendientes, lo ignoramos (el crédito debería estar cerrado)
            if ($allUnpaidInstallments->isEmpty()) {
                return null;
            }

            // Miramos la fecha de la cuota más vieja que debe
            $firstDueDate = Carbon::parse($allUnpaidInstallments->first()->due_date);

            if ($firstDueDate->lessThanOrEqualTo($today)) {

                // TIENE DEUDA (Vencida o de hoy)
                // Filtramos para mostrarle solo las cuotas exigibles hasta hoy
                $dueInstallments = $allUnpaidInstallments->filter(function ($inst) use ($today) {
                    return Carbon::parse($inst->due_date)->lessThanOrEqualTo($today);
                })->values();

                return [
                    'client' => $client,
                    'installments' => $dueInstallments,
                    'total_due' => $dueInstallments->sum(fn ($i) => $i->amount - $i->amount_paid),
                    'is_advance' => false, // Mostrar UI de deuda normal (Rojo/Azul)
                ];
            } else {

                // ESTÁ AL DÍA (Su próxima cuota vence en el futuro)
                // Tomamos SOLO la próxima cuota para sugerir el adelanto
                $nextInstallment = collect([$allUnpaidInstallments->first()]);

                return [
                    'client' => $client,
                    'installments' => $nextInstallment,
                    'total_due' => $nextInstallment->sum(fn ($i) => $i->amount - $i->amount_paid),
                    'is_advance' => true, // Mostrar UI verde de "Adelantar Cuota"
                ];
            }
        })->filter(); // Eliminamos los nulos para limpiar la matriz final

        return view('livewire.collector.dashboard', [
            'collectedToday' => $collectedToday,
            'groupedClients' => $groupedClients,
        ])->layout('layouts.app');
    }
}
