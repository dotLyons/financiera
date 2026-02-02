<?php

namespace App\Livewire\Treasury;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Livewire\Component;

class CollectorDetailModal extends Component
{
    public $isOpen = false;
    public ?User $collector = null;

    protected $listeners = ['openDetailModal'];

    public function openDetailModal($collectorId)
    {
        $this->collector = User::find($collectorId);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['collector']);
    }

    public function render()
    {
        if (!$this->collector) {
            return view('livewire.treasury.collector-detail-modal');
        }

        $today = Carbon::today();

        // 1. RESUMEN DE CAJA (Se mantiene igual, es vital para el admin)
        // Buscamos pagos realizados HOY por este cobrador
        $todaysPayments = PaymentsModel::where('user_id', $this->collector->id)
            ->whereDate('payment_date', $today)
            ->get();

        $cashInHand = $todaysPayments->where('payment_method.value', 'cash')->sum('amount');
        $transfersInHand = $todaysPayments->where('payment_method.value', 'transfer')->sum('amount');
        $totalCollectedToday = $cashInHand + $transfersInHand;

        // 2. LISTA DETALLADA (La nueva lógica agrupada)
        // Buscamos clientes asociados a este cobrador que tengan actividad HOY (Pagos o Deuda)
        $clients = ClientModel::whereHas('credits', function ($q) {
            $q->where('collector_id', $this->collector->id);
        })->get();

        $roadmap = $clients->map(function ($client) use ($today) {
            // A. Cuotas que SE COBRARON HOY (Éxito)
            // Buscamos en sus créditos, cuotas que tengan pagos de hoy hechos por este cobrador
            $paidToday = $client->credits->flatMap(function ($credit) use ($today) {
                return $credit->installments->filter(function ($installment) use ($today) {
                    return $installment->payments->where('user_id', $this->collector->id)
                        ->where('payment_date', '>=', $today->startOfDay())
                        ->where('payment_date', '<=', $today->endOfDay())
                        ->count() > 0;
                });
            });

            // B. Cuotas PENDIENTES EXIGIBLES (Mora o Vencen Hoy)
            // Solo las que vencen hoy o antes y NO están pagadas totalmente
            $pending = $client->credits->flatMap(function ($credit) use ($today) {
                return $credit->installments->filter(function ($installment) use ($today) {
                    return $installment->due_date <= $today && $installment->status->value !== 'paid';
                });
            });

            // Si no tiene nada (ni pagó hoy, ni debe nada vencido), ignoramos al cliente
            if ($paidToday->isEmpty() && $pending->isEmpty()) {
                return null;
            }

            // Calculamos totales para la tarjeta
            $amountPaidToday = $paidToday->sum(fn($i) => $i->payments->where('payment_date', '>=', $today->startOfDay())->sum('amount'));
            $amountPending = $pending->sum(fn($i) => $i->amount - $i->amount_paid);

            return [
                'client' => $client,
                'paid_items' => $paidToday,
                'pending_items' => $pending->sortBy('due_date'), // Ordenamos para ver lo más viejo primero
                'total_paid_today' => $amountPaidToday,
                'total_pending' => $amountPending,
            ];
        })->filter(); // Eliminamos los nulos

        // Totales generales para los KPIs
        $expectedTotal = $totalCollectedToday + $roadmap->sum('total_pending');

        return view('livewire.treasury.collector-detail-modal', [
            'cashInHand' => $cashInHand,
            'transfersInHand' => $transfersInHand,
            'totalCollectedToday' => $totalCollectedToday,
            'expectedTotal' => $expectedTotal,
            'roadmap' => $roadmap,
        ])->layout('layouts.app');
    }
}
