<?php

namespace App\Livewire\Clients;

use App\Src\Client\Models\ClientModel;
use Livewire\Component;

class History extends Component
{
    public ClientModel $client;
    protected $listeners = ['paymentProcessed' => '$refresh'];

    public function mount(ClientModel $client)
    {
        $this->client = $client;
    }

    public function render()
    {
        // Cargamos los créditos ordenados del más reciente al más antiguo.
        // Eager loading de 'installments' para calcular totales sin matar la BD.
        $credits = $this->client->credits()
            ->with('installments')
            ->orderBy('start_date', 'desc')
            ->get();

        // Cálculo de Estadísticas Rápidas
        $stats = [
            'total_credits' => $credits->count(),
            'active_credits' => $credits->where('status.value', 'active')->count(),
            'total_debt' => $credits->where('status.value', 'active')->sum(function ($credit) {
                // Sumamos el saldo restante de todas las cuotas
                return $credit->installments->sum(fn($i) => $i->amount - $i->amount_paid);
            }),
        ];

        return view('livewire.clients.history', [
            'credits' => $credits,
            'stats' => $stats
        ])->layout('layouts.app');
    }
}
