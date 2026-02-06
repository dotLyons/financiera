<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Legajo Diario: <span class="text-indigo-600">{{ $collector->name }}</span>
            </h2>
            <a href="{{ route('collectors.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">&larr;
                Volver</a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Meta (Ruta)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Recaudado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salud de Cobro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($metrics as $metric)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                {{ $metric->date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                $ {{ number_format($metric->expected_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="block font-bold text-gray-800">$
                                    {{ number_format($metric->collected_total, 2) }}</span>
                                <span class="text-xs text-gray-400">
                                    (E: ${{ number_format($metric->collected_cash, 0) }} | T:
                                    ${{ number_format($metric->collected_transfer, 0) }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap align-middle">
                                @php
                                    $p = $metric->performance_percent;
                                    $color =
                                        $p >= 100
                                            ? 'bg-yellow-400'
                                            : ($p >= 80
                                                ? 'bg-green-500'
                                                : ($p >= 50
                                                    ? 'bg-yellow-500'
                                                    : 'bg-red-500'));
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-200 max-w-[150px]">
                                    <div class="{{ $color }} h-2.5 rounded-full"
                                        style="width: {{ $p }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block">{{ number_format($p, 1) }}%</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('report.daily', ['user' => $collector->id, 'date' => $metric->date->format('Y-m-d')]) }}"
                                    target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold flex items-center justify-end">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    PDF Detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Aún no hay registros de cierre diario.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $metrics->links() }}
            </div>
        </div>
    </div>
</div>
