<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-8 flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Panel Principal</h2>
                <p class="text-gray-500 mt-1">Resumen financiero de {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="text-sm text-gray-400">
                Datos actualizados al {{ now()->format('d/m H:i') }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-indigo-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 mr-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Cartera de Clientes</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalClients }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Créditos Activos</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $activeCredits }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-orange-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-50 text-orange-600 mr-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Prestado este Mes</p>
                        <p class="text-2xl font-bold text-gray-800">$ {{ number_format($lentThisMonth, 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Cobrado este Mes</p>
                        <p class="text-2xl font-bold text-gray-800">$ {{ number_format($collectedThisMonth, 0) }}</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="bg-white shadow-sm rounded-lg p-6 lg:col-span-1">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Salud de Cobranza (Mes)</h3>

                <div class="space-y-6">
                    @php
                        // Lógica estricta de colores
                        if ($collectionProgress >= 100) {
                            $barColor = 'bg-yellow-400'; // Dorado visual
                            $textColor = 'text-yellow-600';
                            $statusText = '¡Meta Alcanzada!';
                        } elseif ($collectionProgress >= 80) {
                            $barColor = 'bg-green-500';
                            $textColor = 'text-green-600';
                            $statusText = 'Excelente';
                        } elseif ($collectionProgress >= 50) {
                            $barColor = 'bg-yellow-500';
                            $textColor = 'text-yellow-600';
                            $statusText = 'Regular';
                        } else {
                            $barColor = 'bg-red-500';
                            $textColor = 'text-red-600';
                            $statusText = 'Crítico';
                        }
                    @endphp

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">Meta de Recaudación</span>
                            <span class="{{ $textColor }} font-bold">{{ number_format($collectionProgress, 1) }}%
                                ({{ $statusText }})</span>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="{{ $barColor }} h-4 rounded-full transition-all duration-500 shadow-sm"
                                style="width: {{ $collectionProgress > 100 ? 100 : $collectionProgress }}%">
                            </div>
                        </div>

                        <div class="flex justify-between text-xs text-gray-400 mt-2">
                            <span>Actual: ${{ number_format($collectedThisMonth, 0) }}</span>
                            <span>Esperado: ${{ number_format($expectedCollectionThisMonth, 0) }}</span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden lg:col-span-2">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-red-50">
                    <h3 class="text-lg font-bold text-red-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        Alertas de Mora (Recientes)
                    </h3>
                    <a href="{{ route('credits.index') }}"
                        class="text-xs font-medium text-red-600 hover:text-red-800">Ver todos</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                    Vencimiento</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($overdueInstallments as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->credit->client->full_name }}</div>
                                        <div class="text-xs text-gray-500">Cobrador:
                                            {{ $item->credit->collector->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $item->due_date->format('d/m') }}
                                        </span>
                                        <div class="text-xs text-red-500 mt-1 font-bold">
                                            {{ $item->due_date->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                                        ${{ number_format($item->amount - $item->amount_paid, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('clients.history', $item->credit->client_id) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            Gestionar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm">¡No hay alertas de mora recientes!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
