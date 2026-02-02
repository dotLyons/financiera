<?php

use App\Livewire\Credits\CreateCredit;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // 🚦 CONTROLADOR DE TRÁFICO (Accesible para todos los logueados)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'collector') {
            return redirect()->route('collector.dashboard');
        }
        return redirect()->route('admin.dashboard');
    })->name('dashboard');


    // =========================================================================
    // 🛡️ GRUPO SOLO ADMINISTRADORES
    // =========================================================================
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

        // Cobradores
        Route::get('/collectors', \App\Livewire\Collectors\Index::class)->name('collectors.index');
        Route::get('/collectors/create', \App\Livewire\Collectors\Create::class)->name('collectors.create');
        Route::get('/collectors/{user}/edit', \App\Livewire\Collectors\Edit::class)->name('collectors.edit');
    });


    // =========================================================================
    // 🧢 GRUPO SOLO COBRADORES
    // =========================================================================
    Route::middleware(['role:collector'])->group(function () {

        Route::get('/collector/dashboard', \App\Livewire\Collector\Dashboard::class)->name('collector.dashboard');

        Route::get('/collector/checkout/{installment}', \App\Livewire\Collector\Checkout::class)->name('collector.checkout');
        Route::get('/collector/my-cash', \App\Livewire\Collector\MyCash::class)->name('collector.my-cash');
    });
});
