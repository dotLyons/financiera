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
        Schema::table('installments', function (Blueprint $table) {
            $table->decimal('punitory_paid', 15, 2)->default(0)->after('punitory_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('punitory_paid');
        });
    }
};
