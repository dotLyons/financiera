<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('collectors.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al listado
            </a>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6 mb-8">
            <div class="flex items-center space-x-5">
                <div
                    class="h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center border border-indigo-200 text-indigo-700 text-xl font-bold flex-shrink-0">
                    {{ substr($collector->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                        {{ $collector->name }}
                    </h2>
                    <p class="text-sm text-gray-500 uppercase tracking-wide">
                        Legajo / Historial de Recaudación
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Resúmenes Diarios</h3>
                <p class="text-xs text-gray-500 mt-1">Haga clic en el botón de PDF para descargar el detalle exacto de
                    cada día.</p>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Operaciones
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Recaudado
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Acción
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($metrics as $metric)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($metric->date)->translatedFormat('l, d F Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($metric->date)->format('d/m/Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                    {{ $metric->total_receipts }} cobros
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-green-600">
                                    $ {{ number_format($metric->total_collected, 2) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('report.daily.collector', [$collector->id, $metric->date]) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Descargar PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Este cobrador aún no tiene pagos registrados en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $metrics->links() }}
        </div>
    </div>
</div>
