<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Src\Installments\Models\InstallmentModel;
use Carbon\Carbon;

class ApplyLateFees extends Command
{
    protected $signature = 'credits:apply-late-fees';
    protected $description = 'Aplica interés punitorio a cuotas vencidas según su frecuencia';

    public function handle()
    {
        $today = Carbon::today();

        $overdueInstallments = InstallmentModel::with('credit')
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'paid')
            ->get();

        $count = 0;

        foreach ($overdueInstallments as $installment) {

            $dailyFee = 0;
            $currentPunitory = (float) $installment->punitory_interest;

            $frequency = $installment->credit->payment_frequency;
            $freqValue = is_object($frequency) ? $frequency->value : $frequency;

            if ($freqValue === 'weekly') {
                if ($currentPunitory < 100000) {
                    $dailyFee = 10000;
                } elseif ($currentPunitory < 200000) {
                    $dailyFee = 20000;
                } elseif ($currentPunitory < 300000) {
                    $dailyFee = 30000;
                } else {
                    $dailyFee = 40000;
                }
            } elseif ($freqValue === 'monthly') {
                $daysLate = Carbon::parse($installment->due_date)->diffInDays($today);

                if ($daysLate > 0 && ($daysLate - 1) % 7 === 0) {
                    $creditId = $installment->credit_id;
                    $totalPaid = InstallmentModel::where('credit_id', $creditId)->sum('amount_paid');
                    $totalPunitory = InstallmentModel::where('credit_id', $creditId)->sum('punitory_interest');

                    $saldoRestante = ($installment->credit->amount_total - $totalPaid) + $totalPunitory;

                    if ($saldoRestante > 0) {
                        $dailyFee = $saldoRestante * 0.05;
                    }
                }
            } else {
                $dailyFee = 500;
            }

            if ($dailyFee > 0) {
                $installment->punitory_interest += $dailyFee;
            }

            if ($installment->status->value === 'pending') {
                $installment->status = 'overdue';
            }

            $installment->save();
            $count++;
        }

        $this->info("Proceso terminado. Se revisaron y actualizaron {$count} cuotas vencidas.");
    }
}
