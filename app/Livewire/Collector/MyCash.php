<?php

namespace App\Livewire\Collector;

use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Livewire\Component;

class MyCash extends Component
{
    public function render()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        // Pagos de HOY
        $payments = PaymentsModel::where('user_id', $userId)
            ->whereDate('payment_date', $today)
            ->with('installment.credit.client')
            ->orderBy('created_at', 'desc')
            ->get();

        $cash = $payments->where('payment_method.value', 'cash')->sum('amount');
        $transfer = $payments->where('payment_method.value', 'transfer')->sum('amount');

        return view('livewire.collector.my-cash', [
            'payments' => $payments,
            'cash' => $cash,
            'transfer' => $transfer,
            'total' => $cash + $transfer
        ])->layout('layouts.app');
    }
}
