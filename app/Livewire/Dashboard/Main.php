<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Src\Client\Models\ClientModel as ModelsClientModel;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Models\InstallmentModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Main extends Component
{
    use WithPagination;

    public function render()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totalClients = ModelsClientModel::count();

        $activeCredits = CreditsModel::where('status', 'active')->count();

        $collectedMonth = DB::table('payments')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $lentMonth = CreditsModel::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount_net');

        $expectedMonth = InstallmentModel::whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $pendingMonth = InstallmentModel::whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->sum(DB::raw('amount - amount_paid'));

        $collectionHealth = $expectedMonth > 0 ? (($expectedMonth - $pendingMonth) / $expectedMonth) * 100 : 0;


        $totalOutstanding = InstallmentModel::where('status', '!=', 'paid')
            ->sum(DB::raw('amount - amount_paid'));

        $totalPortfolio = InstallmentModel::sum(DB::raw('amount - amount_paid'));
        $overdueAmount = InstallmentModel::where('status', 'overdue')->sum(DB::raw('amount - amount_paid'));
        $defaultRate = $totalPortfolio > 0 ? ($overdueAmount / $totalPortfolio) * 100 : 0;

        $lateInstallments = InstallmentModel::with(['credit.client'])
            ->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('due_date', '<', now())->where('status', '!=', 'paid');
            })
            ->orderBy('due_date', 'asc')
            ->paginate(3);

        $chartData = $this->getChartData();

        $collectorsRanking = User::where('role', 'collector')
            ->get()
            ->map(function ($collector) use ($startOfMonth, $endOfMonth) {

                $goal = InstallmentModel::whereHas('credit', function ($q) use ($collector) {
                    $q->where('collector_id', $collector->id);
                })->whereBetween('due_date', [$startOfMonth, $endOfMonth])->sum('amount');

                $actual = DB::table('payments')
                    ->where('user_id', $collector->id)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                $percentage = $goal > 0 ? ($actual / $goal) * 100 : 0;

                return (object) [
                    'name' => $collector->name,
                    'email' => $collector->email,
                    'goal' => $goal,
                    'actual' => $actual,
                    'percentage' => $percentage
                ];
            })
            ->sortByDesc('percentage');

        return view('livewire.dashboard.main', [
            'totalClients' => $totalClients,
            'activeCredits' => $activeCredits,
            'collectedMonth' => $collectedMonth,
            'lentMonth' => $lentMonth,
            'pendingMonth' => $pendingMonth,
            'expectedMonth' => $expectedMonth,
            'collectionHealth' => $collectionHealth,
            'totalOutstanding' => $totalOutstanding,
            'defaultRate' => $defaultRate,
            'lateInstallments' => $lateInstallments,
            'chartLabels' => $chartData['labels'],
            'chartIncome' => $chartData['income'],
            'collectorsRanking' => $collectorsRanking,
        ])->layout('layouts.app');
    }

    private function getChartData()
    {
        $labels = [];
        $income = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            $income[] = DB::table('payments')->whereDate('payment_date', $date)->sum('amount');
        }

        return ['labels' => $labels, 'income' => $income];
    }
}
