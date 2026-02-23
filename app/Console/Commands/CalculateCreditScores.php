<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Src\Client\Models\ClientModel;
use Carbon\Carbon;

class CalculateCreditScores extends Command
{
    protected $signature = 'clients:calculate-scores';
    protected $description = 'Analiza el historial de pagos y asigna un nivel de riesgo a cada cliente';

    public function handle()
    {
        $this->info("Iniciando análisis de riesgo crediticio...");

        $clients = ClientModel::with(['credits.installments'])->get();
        $today = Carbon::today();
        $count = 0;

        foreach ($clients as $client) {
            $credits = $client->credits;

            if ($credits->isEmpty()) {
                $this->updateScore($client, 0, 'Sin historial crediticio registrado.');
                continue;
            }

            $hasDefaulted = $credits->contains(function($c) {
                $status = is_object($c->status) ? $c->status->value : $c->status;
                return $status === 'defaulted';
            });

            if ($hasDefaulted) {
                $this->updateScore($client, 5, 'Riesgo Extremo: Posee créditos incobrables/abandonados.');
                continue;
            }

            $allInstallments = $credits->flatMap->installments;

            $overdueInstallments = $allInstallments->filter(function($i) use ($today) {
                $status = is_object($i->status) ? $i->status->value : $i->status;
                return $status === 'overdue' && Carbon::parse($i->due_date)->lessThan($today);
            });

            $maxDaysLate = 0;
            if ($overdueInstallments->isNotEmpty()) {
                $maxDaysLate = $overdueInstallments->map(fn($i) => Carbon::parse($i->due_date)->diffInDays($today))->max();
            }

            if ($maxDaysLate > 60) {
                $this->updateScore($client, 5, "Riesgo Extremo: Deuda activa con {$maxDaysLate} días de atraso.");
                continue;
            }
            if ($maxDaysLate > 30) {
                $this->updateScore($client, 4, "Riesgo Alto: Deuda activa con {$maxDaysLate} días de atraso.");
                continue;
            }

            $paidInstallments = $allInstallments->filter(function($i) {
                $status = is_object($i->status) ? $i->status->value : $i->status;
                return $status === 'paid';
            });
            $totalPaid = $paidInstallments->count();

            if ($totalPaid === 0 && $overdueInstallments->isEmpty()) {
                $this->updateScore($client, 1, 'Crédito en curso impecable, pero sin pagos finalizados aún.');
                continue;
            } elseif ($totalPaid === 0 && $overdueInstallments->isNotEmpty()) {
                $this->updateScore($client, 3, 'Primer crédito y ya presenta atrasos tempranos.');
                continue;
            }

            $punctualPaid = $paidInstallments->filter(fn($i) => $i->punitory_interest <= 0)->count();
            $punctualityRate = ($punctualPaid / $totalPaid) * 100;

            if ($punctualityRate >= 90 && $overdueInstallments->isEmpty()) {
                $this->updateScore($client, 1, 'Excelente: Puntualidad del ' . round($punctualityRate) . '%.');
            } elseif ($punctualityRate >= 75) {
                $note = 'Bueno: Puntualidad del ' . round($punctualityRate) . '%.';
                if ($overdueInstallments->isNotEmpty()) $note .= ' (Atrasos menores activos).';
                $this->updateScore($client, 2, $note);
            } elseif ($punctualityRate >= 50) {
                $this->updateScore($client, 3, 'Regular: Puntualidad del ' . round($punctualityRate) . '%. Suele generar recargos.');
            } else {
                $this->updateScore($client, 4, 'Riesgoso: Puntualidad del ' . round($punctualityRate) . '%. Historial de pagos tardíos constante.');
            }

            $count++;
        }

        $this->info("¡Listo! Se analizó y actualizó el nivel de riesgo de {$count} clientes.");
    }

    private function updateScore($client, $score, $notes)
    {
        $client->credit_score = $score;
        $client->credit_score_notes = $notes;
        $client->save();
    }
}
