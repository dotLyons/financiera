<?php

use App\Http\Controllers\ReportController;
use App\Livewire\Credits\CreateCredit;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hora', function () {
    return [
        'hora_servidor' => now()->format('Y-m-d H:i:s'),
        'zona_horaria' => config('app.timezone'),
        'diferencia_con_tu_reloj' => 'Debería ser la misma hora que tienes ahora mismo en tu PC/Celular'
    ];
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // =========================================================================
    // 🌑 VISTA DE SISTEMA CERRADO (Accesible fuera de horario)
    // =========================================================================
    // Esta ruta DEBE estar fuera del middleware 'hours' para que el usuario
    // pueda ser redirigido aquí sin causar un error de "demasiadas redirecciones".
    Route::get('/system-closed', function () {
        return view('system-closed');
    })->name('system.closed');


    // =========================================================================
    // ⏰ GRUPO PROTEGIDO POR HORARIO (08:00 - 23:59)
    // =========================================================================
    // Todo lo que esté aquí adentro será inaccesible si se intenta entrar
    // fuera del horario permitido definido en App\Http\Middleware\CheckSystemHours
    Route::middleware(['hours'])->group(function () {
        // 🚦 CONTROLADOR DE TRÁFICO (Redirección inteligente)
        Route::get('/dashboard', function () {
            $user = auth()->user();
            if ($user->role === 'collector') {
                return redirect()->route('collector.dashboard');
            }
            return redirect()->route('admin.dashboard');
        })->name('dashboard');

        // 🛡️ GRUPO SOLO ADMINISTRADORES
        Route::middleware(['role:admin'])->group(function () {
            // Dashboard Admin
            Route::get('/admin/dashboard', \App\Livewire\Dashboard\Main::class)->name('admin.dashboard');

            // Tesorería
            Route::get('/treasury', \App\Livewire\Treasury\Index::class)->name('treasury.index');

            // Créditos
            Route::get('/credits', \App\Livewire\Credits\Index::class)->name('credits.index');
            Route::get('/credits/create', CreateCredit::class)->name('credits.create');

            // Clientes
            Route::get('/clients', \App\Livewire\Clients\Index::class)->name('clients.index');
            Route::get('/clients/create', \App\Livewire\Clients\Create::class)->name('clients.create');
            Route::get('/clients/{client}/edit', \App\Livewire\Clients\Edit::class)->name('clients.edit');
            Route::get('/clients/{client}/history', \App\Livewire\Clients\History::class)->name('clients.history');

            // Rutas para Reportes PDF
            Route::get('/report/client/{client}', [ReportController::class, 'clientSummary'])->name('report.client');
            Route::get('/report/credit/{credit}', [ReportController::class, 'creditDetail'])->name('report.credit');
            Route::get('/contract/new/{credit}', [ReportController::class, 'printContract'])->name('contract.new');
            Route::get('/contract/refinance/{credit}', [ReportController::class, 'printRefinance'])->name('contract.refinance');
            Route::get('/receipt/{payment}', [ReportController::class, 'printPaymentReceipt'])->name('receipt.print');
            Route::get('/report/daily/{user}/{date}', [ReportController::class, 'printDailyReport'])->name('report.daily');

            // Cobradores
            Route::get('/collectors', \App\Livewire\Collectors\Index::class)->name('collectors.index');
            Route::get('/collectors/create', \App\Livewire\Collectors\Create::class)->name('collectors.create');
            Route::get('/collectors/{user}/edit', \App\Livewire\Collectors\Edit::class)->name('collectors.edit');
            Route::get('/collectors/{user}/history', \App\Livewire\Collectors\DailyHistory::class)->name('collectors.history');

            // Pantalla de Reportes
            Route::get('/reports', \App\Livewire\Reports\Index::class)->name('reports.index');

            // Descarga PDF
            Route::get('/report/monthly/{month}/{year}', [ReportController::class, 'printMonthlyReport'])->name('report.monthly');
        });


        // 🧢 GRUPO SOLO COBRADORES
        Route::middleware(['role:collector'])->group(function () {
            Route::get('/collector/dashboard', \App\Livewire\Collector\Dashboard::class)->name('collector.dashboard');
            Route::get('/collector/checkout/{installment}', \App\Livewire\Collector\Checkout::class)->name('collector.checkout');
            Route::get('/collector/my-cash', \App\Livewire\Collector\MyCash::class)->name('collector.my-cash');
        });
    }); // Fin del grupo 'hours'

});
