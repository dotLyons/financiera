<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Livewire\Component;

class Main extends Component
{
    public function render()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. KPIs Generales
        $totalClients = ClientModel::count();
        $activeCredits = CreditsModel::where('status', 'active')->count();

        // 2. Flujo de Caja del MES ACTUAL
        // Dinero que salió (Prestado) este mes
        $lentThisMonth = CreditsModel::whereBetween('start_date', [$startOfMonth, $endOfMonth])
            ->sum('amount_net');

        // Dinero que entró (Cobrado) este mes
        $collectedThisMonth = PaymentsModel::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 3. Alertas de Mora (Top 5 más recientes)
        // Cuotas vencidas, no pagadas, ordenadas por fecha de vencimiento (las más antiguas primero son más críticas)
        $overdueInstallments = InstallmentModel::with(['credit.client', 'credit.collector'])
            ->where('due_date', '<', $now) // Ya venció
            ->where('status', '!=', 'paid') // No está pagada
            ->orderBy('due_date', 'desc') // Las más recientes vencidas arriba (o asc para las más viejas)
            ->take(5)
            ->get();

        // 4. Rendimiento de Cobranza (Para la barra de progreso)
        // Cuánto debíamos cobrar este mes vs cuánto cobramos realmente
        $expectedCollectionThisMonth = InstallmentModel::whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $collectionProgress = $expectedCollectionThisMonth > 0
            ? ($collectedThisMonth / $expectedCollectionThisMonth) * 100
            : 0;

        return view('livewire.dashboard.main', [
            'totalClients' => $totalClients,
            'activeCredits' => $activeCredits,
            'lentThisMonth' => $lentThisMonth,
            'collectedThisMonth' => $collectedThisMonth,
            'overdueInstallments' => $overdueInstallments,
            'expectedCollectionThisMonth' => $expectedCollectionThisMonth,
            'collectionProgress' => $collectionProgress,
        ])->layout('layouts.app');
    }
}
