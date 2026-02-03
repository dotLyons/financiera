<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            // Fecha para controlar el "Enfriamiento" semanal
            $table->timestamp('last_penalty_applied_at')->nullable()->after('status');
        });

        Schema::table('installments', function (Blueprint $table) {
            // Para saber cuánto aumentó la cuota por castigo
            $table->decimal('penalty_amount', 10, 2)->default(0)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('last_penalty_applied_at');
        });
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('penalty_amount');
        });
    }
};
