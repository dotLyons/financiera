<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collector_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // El cobrador
            $table->date('date'); // El día del registro

            // Métricas Financieras
            $table->decimal('expected_amount', 12, 2)->default(0); // Lo que tenía en su ruta (Meta)
            $table->decimal('collected_cash', 12, 2)->default(0);  // Recaudado Efectivo
            $table->decimal('collected_transfer', 12, 2)->default(0); // Recaudado Transf.
            $table->decimal('collected_total', 12, 2)->default(0); // Suma total

            // Métricas de Rendimiento
            $table->decimal('performance_percent', 5, 2)->default(0); // 0 a 100% (Salud)

            $table->timestamps();

            // Evitar duplicados por cobrador/día
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collector_daily_metrics');
    }
};
