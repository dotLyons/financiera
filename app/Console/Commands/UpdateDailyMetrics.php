<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// Modelos
use App\Models\User;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;

class UpdateDailyMetrics extends Command
{
    // El nombre clave para llamar al comando
    protected $signature = 'metrics:update';

    protected $description = 'Calcula y cierra las métricas de cobranza del día actual.';

    public function handle()
    {
        // 1. Definimos "HOY" (Usando zona horaria configurada en app)
        $today = Carbon::today();

        $this->info("📅 Iniciando Cierre Diario: " . $today->format('d/m/Y'));

        // 2. Traemos a todos los cobradores activos
        $collectors = User::where('role', 'collector')->where('is_active', true)->get();

        foreach ($collectors as $collector) {

            // A. META: ¿Cuánto vencía hoy de sus clientes?
            $expectedAmount = InstallmentModel::whereHas('credit', function ($q) use ($collector) {
                $q->where('collector_id', $collector->id)
                    ->where('status', 'active'); // Solo créditos activos
            })
                ->whereDate('due_date', $today)
                ->sum('amount');

            // B. REALIDAD: ¿Cuánto cobró hoy realmente?
            $collectedAmount = PaymentsModel::where('user_id', $collector->id)
                ->whereDate('payment_date', $today)
                ->sum('amount');

            // C. EFICIENCIA: Porcentaje
            $efficiency = $expectedAmount > 0 ? ($collectedAmount / $expectedAmount) * 100 : 0;

            // D. GUARDAR EN BD (Sin duplicar)
            // Buscamos por ID y FECHA. Si existe, actualiza. Si no, crea.
            DB::table('collector_daily_metrics')->updateOrInsert(
                [
                    'user_id' => $collector->id,
                    'date' => $today->format('Y-m-d')
                ],
                [
                    'expected_amount' => $expectedAmount,
                    'collected_amount' => $collectedAmount, // Usamos el nombre que sabemos que funciona
                    'efficiency_percentage' => $efficiency,
                    'updated_at' => now(),
                    'created_at' => now(), // Nota: updateOrInsert solo usa esto al crear
                ]
            );

            $this->info(" > {$collector->name}: Meta \${$expectedAmount} | Cobrado \${$collectedAmount}");
        }

        $this->info("🚀 Cierre diario completado con éxito.");
    }
}
