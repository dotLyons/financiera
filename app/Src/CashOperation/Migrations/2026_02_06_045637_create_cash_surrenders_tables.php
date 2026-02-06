<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_surrenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collector_id')->constrained('users');
            $table->foreignId('admin_id')->constrained('users');
            $table->decimal('amount', 12, 2); // Cuánto dinero rinde
            $table->string('payment_method'); // 'cash' o 'transfer' (para saber qué rindió)
            $table->text('notes')->nullable(); // Observaciones ("Faltaron 100 pesos", "Entrega parcial", etc)
            $table->timestamp('surrendered_at'); // Fecha y hora exacta (puede diferir del created_at si se carga tarde)
            $table->timestamps(); // Created_at y Updated_at
            $table->index('collector_id');
            $table->index('surrendered_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet_balance', 12, 2)->default(0)->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });
        Schema::dropIfExists('cash_surrenders');
    }
};
