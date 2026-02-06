<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Reportes Mensuales</h2>

            <div class="flex space-x-4">
                <select wire:model.live="selectedMonth" class="rounded-md border-gray-300 shadow-sm ...">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">
                            {{ ucfirst(\Carbon\Carbon::create()->month($m)->locale('es')->monthName) }}
                        </option>
                    @endforeach
                </select>

                <select wire:model.live="selectedYear"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach (range(now()->year, 2024) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($stats)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Ingresos Totales (Cobrado)</p>
                    <p class="text-2xl font-bold text-green-700 mt-1">$
                        {{ number_format($stats['financial']['inflow_total'], 2) }}</p>
                    <div class="mt-2 text-xs text-gray-400">
                        Efectivo: ${{ number_format($stats['financial']['inflow_cash'], 0) }}<br>
                        Transf: ${{ number_format($stats['financial']['inflow_transfer'], 0) }}
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Salidas (Créditos Otorgados)</p>
                    <p class="text-2xl font-bold text-red-700 mt-1">$
                        {{ number_format($stats['financial']['outflow_credits'], 2) }}</p>
                    <p class="mt-2 text-xs text-gray-400">{{ $stats['operational']['credits_count'] }} créditos nuevos
                    </p>
                </div>

                <div
                    class="bg-white p-6 rounded-lg shadow-sm border-l-4 {{ $stats['financial']['net_result'] >= 0 ? 'border-indigo-500' : 'border-orange-500' }}">
                    <p class="text-sm font-medium text-gray-500 uppercase">Flujo Neto del Mes</p>
                    <p
                        class="text-3xl font-bold {{ $stats['financial']['net_result'] >= 0 ? 'text-indigo-700' : 'text-orange-600' }} mt-1">
                        $ {{ number_format($stats['financial']['net_result'], 2) }}
                    </p>
                    <p class="mt-2 text-xs text-gray-400">Dinero real que quedó o faltó</p>
                </div>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-8 text-center">
                <h3 class="text-lg font-bold text-indigo-900 mb-2">Informe Gerencial Completo</h3>
                <p class="text-indigo-600 mb-6 text-sm">Descarga el PDF detallado con análisis de cobradores, gráficos
                    de rendimiento y balance operativo.</p>

                <a href="{{ route('report.monthly', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                    target="_blank"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Descargar Reporte de {{ $stats['period'] }}
                </a>
            </div>
        @endif
    </div>
</div>
