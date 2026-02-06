<?php

namespace App\Livewire\Treasury;

use App\Models\User;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;
use App\Src\CashOperation\Models\CashSurrender;
use App\Src\CashOperation\Models\CashSurrenderModel;
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

        $collectors = User::where('role', 'collector')
            ->where('is_active', true)
            ->get()
            ->map(function ($collector) use ($today, $startOfMonth, $endOfMonth) {
                $collector->total_today = $collector->wallet_balance;
                $collector->cash_in_hand = $collector->wallet_balance;
                $collector->transfers_in_hand = 0;

                $expectedMonth = InstallmentModel::whereHas('credit', function ($q) use ($collector) {
                    $q->where('collector_id', $collector->id)->where('status', 'active');
                })->whereBetween('due_date', [$startOfMonth, $endOfMonth])->sum('amount');

                $collectedMonth = PaymentsModel::where('user_id', $collector->id)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('amount');

                $collector->monthly_goal_percent = $expectedMonth > 0 ? ($collectedMonth / $expectedMonth) * 100 : 0;

                return $collector;
            });

        $adminsWithCash = User::where('role', 'admin')
            ->where('wallet_balance', '>', 0.01) // Solo si tienen saldo positivo
            ->get();

        $moneyOnStreet = $collectors->sum('wallet_balance') + $adminsWithCash->sum('wallet_balance');
        $moneyInOffice = CashSurrenderModel::sum('amount');

        return view('livewire.treasury.index', [
            'collectors' => $collectors,
            'adminsWithCash' => $adminsWithCash,
            'totalCash' => $moneyInOffice,
            'grandTotal' => $moneyOnStreet,
            'totalTransfer' => 0,
        ])->layout('layouts.app');
    }
}
