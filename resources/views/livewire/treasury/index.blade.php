<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Tesorería y Caja</h2>
                <p class="text-sm text-gray-500 mt-1">Control de flujo de efectivo y rendiciones de empleados.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">En Caja Central (Oficina)
                        </p>
                        <p class="text-3xl font-bold text-gray-900">$
                            {{ number_format($totalCash + $totalTransfer, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">En manos de
                            Cobradores/Admin</p>
                        <p class="text-3xl font-bold text-gray-900">$ {{ number_format($grandTotal, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($adminsWithCash) && $adminsWithCash->count() > 0)
            <h3 class="text-lg font-medium text-gray-900 mb-4 px-1 mt-8 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Caja Chica / Administración
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach ($adminsWithCash as $admin)
                    <div
                        class="bg-indigo-50 border border-indigo-200 shadow-sm rounded-lg overflow-hidden flex flex-col relative">
                        <div
                            class="absolute top-0 right-0 bg-indigo-600 text-white text-[10px] px-2 py-1 rounded-bl-lg uppercase font-bold tracking-wider">
                            Administración
                        </div>

                        <div class="p-5 flex-1">
                            <div class="flex items-center mb-4 border-b border-indigo-200 pb-4">
                                <div
                                    class="h-10 w-10 rounded-full bg-white border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold mr-3">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800 leading-tight">{{ $admin->name }}</h4>
                                    <span class="text-xs text-gray-500">Recaudación Propia</span>
                                </div>
                            </div>

                            <div class="text-center mb-2">
                                <span class="text-xs text-indigo-600 uppercase font-semibold">Saldo Disponible</span>
                                <div class="text-3xl font-bold text-indigo-800">
                                    $ {{ number_format($admin->wallet_balance, 2) }}
                                </div>
                            </div>
                        </div>

                        <div class="bg-indigo-100 px-6 py-4 border-t border-indigo-200">
                            {{-- Usamos el MISMO modal de rendición --}}
                            <button
                                wire:click="$dispatch('openSurrenderModal', { collectorId: {{ $admin->id }}, currentBalance: {{ $admin->wallet_balance }} })"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-800 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                    </path>
                                </svg>
                                Ingresar a Caja Central
                            </button>
                            <p class="text-[10px] text-center text-indigo-500 mt-2">
                                * Registrar ingreso de dinero a la caja fuerte
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <h3 class="text-lg font-medium text-gray-900 mb-4 px-1">Saldos por Cobrador (Billetera Activa)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($collectors as $collector)
                <div
                    class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden flex flex-col hover:shadow-md transition">
                    <div class="p-5 flex-1">

                        <div class="flex items-center mb-4 border-b border-gray-100 pb-4">
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-3">
                                {{ substr($collector->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 leading-tight">{{ $collector->name }}</h4>
                                <span class="text-xs text-gray-500">{{ $collector->email }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <div class="bg-green-50 rounded p-2 border border-green-100 text-center">
                                <span class="block text-[10px] text-green-600 font-bold uppercase tracking-wide">Saldo
                                    Efectivo</span>
                                <span class="block text-lg font-bold text-gray-800">$
                                    {{ number_format($collector->cash_in_hand, 0) }}</span>
                            </div>
                            <div class="bg-blue-50 rounded p-2 border border-blue-100 text-center opacity-50">
                                <span
                                    class="block text-[10px] text-blue-600 font-bold uppercase tracking-wide">Transferencia</span>
                                {{-- Billetera unificada por ahora --}}
                                <span class="block text-lg font-bold text-gray-800">$
                                    {{ number_format($collector->transfers_in_hand, 0) }}</span>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <span class="text-xs text-gray-400 uppercase">Total en Billetera (A Rendir)</span>
                            <div
                                class="text-2xl font-bold {{ $collector->total_today > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                                $ {{ number_format($collector->total_today, 2) }}
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            @php
                                $percent = $collector->monthly_goal_percent ?? 0;
                                if ($percent >= 100) {
                                    $barColor = 'bg-yellow-400';
                                    $textColor = 'text-yellow-600';
                                } elseif ($percent >= 80) {
                                    $barColor = 'bg-green-500';
                                    $textColor = 'text-green-600';
                                } elseif ($percent >= 50) {
                                    $barColor = 'bg-yellow-500';
                                    $textColor = 'text-yellow-600';
                                } else {
                                    $barColor = 'bg-red-500';
                                    $textColor = 'text-red-600';
                                }
                            @endphp

                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500 font-medium">Meta Mensual</span>
                                <span class="font-bold {{ $textColor }}">
                                    {{ number_format($percent, 1) }}%
                                </span>
                            </div>

                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500"
                                    style="width: {{ $percent > 100 ? 100 : $percent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 space-y-2">

                        {{-- BOTÓN RECIBIR RENDICIÓN --}}
                        @if ($collector->total_today > 0.01)
                            <button
                                wire:click="$dispatch('openSurrenderModal', { collectorId: {{ $collector->id }}, currentBalance: {{ $collector->total_today }} })"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Recibir Rendición
                            </button>
                        @else
                            <button disabled
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-bold text-xs text-gray-400 uppercase tracking-widest cursor-not-allowed">
                                Sin saldo pendiente
                            </button>
                        @endif

                        {{-- BOTÓN VER DETALLE --}}
                        <button wire:click="$dispatch('openDetailModal', { collectorId: {{ $collector->id }} })"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            Ver Detalle / Ruta
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    @livewire('treasury.surrender-modal')
    @livewire('treasury.collector-detail-modal')
</div>
