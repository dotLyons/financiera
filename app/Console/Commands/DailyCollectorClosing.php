<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Src\Collectors\Models\CollectorDailyMetric;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyCollectorClosing extends Command
{
    protected $signature = 'collectors:daily-close';
    protected $description = 'Genera el cierre diario de métricas para cada cobrador';

    public function handle()
    {
        $today = Carbon::today(); // O Carbon::yesterday() si se corre a las 00:01
        $collectors = User::where('role', 'collector')->where('is_active', true)->get();

        $this->info("Iniciando cierre para el día: " . $today->format('d/m/Y'));

        foreach ($collectors as $collector) {
            // 1. Calcular META del día (Hoja de Ruta)
            // Son todas las cuotas asignadas que vencían hoy o antes
            // Y que NO estaban pagadas antes de empezar el día (esto es una aproximación,
            // para ser exactos tomamos lo que vence hoy + mora activa).
            $expected = InstallmentModel::whereHas('credit', fn($q) => $q->where('collector_id', $collector->id)->where('status', 'active'))
                ->where('due_date', '<=', $today) // Vence hoy o ya venció
                ->where('status', '!=', 'paid') // Y sigue pendiente (o se pagó HOY, por eso incluimos logicamente lo cobrado)
                ->sum(function ($installment) {
                    return $installment->amount - $installment->amount_paid;
                });

            // Corrección lógica: Si ya pagó hoy, el status en BD puede ser 'paid'.
            // Para la "Meta", debemos sumar TAMBIÉN lo que cobró hoy, porque al inicio del día era deuda.
            $collectedToday = PaymentsModel::where('user_id', $collector->id)
                ->whereDate('payment_date', $today)
                ->sum('amount');

            // La meta real era: Lo que todavía deben + Lo que pagaron hoy
            $realTarget = $expected + $collectedToday;

            // 2. Desglose de Cobros
            $cash = PaymentsModel::where('user_id', $collector->id)
                ->whereDate('payment_date', $today)
                ->where('payment_method', 'cash')
                ->sum('amount');

            $transfer = PaymentsModel::where('user_id', $collector->id)
                ->whereDate('payment_date', $today)
                ->where('payment_method', 'transfer')
                ->sum('amount');

            // 3. Salud (Porcentaje)
            $percent = $realTarget > 0 ? ($collectedToday / $realTarget) * 100 : 0;
            if ($percent > 100) $percent = 100; // Por si acaso

            // 4. Guardar
            CollectorDailyMetric::updateOrCreate(
                ['user_id' => $collector->id, 'date' => $today],
                [
                    'expected_amount' => $realTarget,
                    'collected_cash' => $cash,
                    'collected_transfer' => $transfer,
                    'collected_total' => $collectedToday,
                    'performance_percent' => $percent,
                ]
            );

            $this->info("Cierre generado para: {$collector->name}");
        }
    }
}
