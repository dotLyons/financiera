<?php

namespace App\Livewire\Collectors;

use App\Models\User;
use App\Src\Payments\Models\PaymentsModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class DailyHistory extends Component
{
    use WithPagination;

    public User $collector;

    public function mount(User $user)
    {
        $this->collector = $user;
    }

    public function render()
    {
        $metrics = PaymentsModel::where('user_id', $this->collector->id)
            ->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('COUNT(id) as total_receipts'),
                DB::raw('SUM(amount) as total_collected')
            )
            ->groupBy(DB::raw('DATE(payment_date)'))
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('livewire.collectors.daily-history', [
            'metrics' => $metrics
        ])->layout('layouts.app');
    }
}
