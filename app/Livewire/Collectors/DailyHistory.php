<?php

namespace App\Livewire\Collectors;

use App\Models\User;
use App\Src\Collectors\Models\CollectorDailyMetric;
use Livewire\Component;
use Livewire\WithPagination;

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
        $metrics = CollectorDailyMetric::where('user_id', $this->collector->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('livewire.collectors.daily-history', [
            'metrics' => $metrics
        ])->layout('layouts.app');
    }
}
