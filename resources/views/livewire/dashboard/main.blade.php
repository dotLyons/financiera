<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Panel de Control</h2>
                <p class="text-sm text-gray-500">Resumen operativo al {{ now()->format('d/m/Y') }}</p>
            </div>
            <div>
                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">
                    Admin
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Clientes</p>
                    <p class="text-lg font-bold text-gray-800">{{ $totalClients }}</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-purple-50 text-purple-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Créditos Activos</p>
                    <p class="text-lg font-bold text-gray-800">{{ $activeCredits }}</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 md:col-span-2">
                @php
                    if ($collectionHealth >= 100) {
                        $barColor = 'bg-yellow-400';
                        $textColor = 'text-yellow-700';
                        $statusText = '¡Meta Alcanzada!';
                    } elseif ($collectionHealth >= 80) {
                        $barColor = 'bg-green-500';
                        $textColor = 'text-green-600';
                        $statusText = 'Excelente';
                    } elseif ($collectionHealth >= 50) {
                        $barColor = 'bg-yellow-500';
                        $textColor = 'text-yellow-600';
                        $statusText = 'Regular';
                    } else {
                        $barColor = 'bg-red-500';
                        $textColor = 'text-red-600';
                        $statusText = 'Crítico';
                    }
                @endphp
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Salud de Cobranza (Mes)</p>
                        <p class="text-xs text-gray-400">Meta: ${{ number_format($expectedMonth, 2, ',', '.') }}</p>
                    </div>
                    <span class="text-lg font-bold {{ $textColor }}">
                        {{ number_format($collectionHealth, 1, ',', '.') }}% <span
                            class="text-xs font-normal">({{ $statusText }})</span>
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500"
                        style="width: {{ $collectionHealth > 100 ? 100 : $collectionHealth }}%">
                    </div>
                </div>
                <div class="flex justify-between text-xs mt-1 text-gray-500">
                    <span>Cobrado: ${{ number_format($collectedMonth, 2, ',', '.') }}</span>
                    <span>Falta: ${{ number_format($pendingMonth, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-orange-400">
                <p class="text-sm text-gray-500 font-bold uppercase">Falta Cobrar (Este Mes)</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($pendingMonth, 2, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-1">Vencimientos pendientes de Febrero</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500">
                <p class="text-sm text-gray-500 font-bold uppercase">Cobrado (Este Mes)</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($collectedMonth, 2, ',', '.') }}
                </h3>
                <p class="text-xs text-green-600 mt-1">+ Cash Flow ingresado</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-600">
                <p class="text-sm text-gray-500 font-bold uppercase">Capital en la Calle (Total)</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($totalOutstanding, 2, ',', '.') }}
                </h3>
                <p class="text-xs text-blue-600 mt-1">Saldo Vivo Total</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow lg:col-span-2 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">🚨 Clientes en Mora (Prioridad)</h3>
                        <a href="{{ route('credits.index') }}" class="text-xs text-blue-600 hover:underline">Ver
                            todos</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Cliente</th>
                                    <th class="px-6 py-3">Vencimiento</th>
                                    <th class="px-6 py-3">Deuda Cuota</th>
                                    <th class="px-6 py-3">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lateInstallments as $installment)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $installment->credit->client->last_name ?? '' }}
                                            {{ $installment->credit->client->first_name ?? 'Desconocido' }}
                                            <div class="text-xs text-gray-500">Crédito #{{ $installment->credit_id }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="text-red-600 font-bold">{{ \Carbon\Carbon::parse($installment->due_date)->format('d/m') }}</span>
                                            <span
                                                class="text-xs text-gray-400 block">({{ \Carbon\Carbon::parse($installment->due_date)->diffForHumans() }})</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            ${{ number_format($installment->amount - $installment->amount_paid, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('clients.history', $installment->credit->client_id) }}"
                                                class="text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-xs px-3 py-2 text-center inline-flex items-center">Gestionar</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">🎉 ¡Excelente! No
                                            hay
                                            cuotas vencidas pendientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $lateInstallments->links() }}
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-5" wire:ignore>
                <h4 class="font-bold text-gray-800 mb-4">Ingresos (7 días)</h4>
                <div id="miniChart" style="min-height: 250px;"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                        </path>
                    </svg>
                    Ranking de Efectividad (Cobradores)
                </h3>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($collectorsRanking as $collector)
                    @php
                        if ($collector->percentage >= 100) {
                            $barColor = 'bg-yellow-400';
                            $textColor = 'text-yellow-700';
                            $statusText = '¡Imparable! 🏆';
                        } elseif ($collector->percentage >= 80) {
                            $barColor = 'bg-green-500';
                            $textColor = 'text-green-600';
                            $statusText = 'Excelente';
                        } elseif ($collector->percentage >= 50) {
                            $barColor = 'bg-yellow-500';
                            $textColor = 'text-yellow-600';
                            $statusText = 'En camino';
                        } else {
                            $barColor = 'bg-red-500';
                            $textColor = 'text-red-600';
                            $statusText = 'Bajo Rendimiento';
                        }
                    @endphp

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 relative">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-bold text-gray-800">{{ $collector->name }}</p>
                                <p class="text-xs text-gray-500">Meta:
                                    ${{ number_format($collector->goal, 2, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold {{ $textColor }}">
                                    {{ number_format($collector->percentage, 1, ',', '.') }}%</p>
                                <p class="text-[10px] uppercase font-bold {{ $textColor }}">{{ $statusText }}</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                            <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500"
                                style="width: {{ $collector->percentage > 100 ? 100 : $collector->percentage }}%">
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Cobrado: <span
                                    class="font-semibold text-gray-700">${{ number_format($collector->actual, 2, ',', '.') }}</span></span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-4 text-gray-400">No hay cobradores registrados o activos este
                        mes.</div>
                @endforelse
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            var options = {
                series: [{
                    name: 'Cobrado',
                    data: @json($chartIncome)
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#10B981'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '50%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "$ " + val.toLocaleString('es-ES', {
                                minimumFractionDigits: 2
                            });
                        }
                    }
                },
                xaxis: {
                    categories: @json($chartLabels),
                    labels: {
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                grid: {
                    show: false
                }
            };
            var chart = new ApexCharts(document.querySelector("#miniChart"), options);
            chart.render();
        });
    </script>
</div>
