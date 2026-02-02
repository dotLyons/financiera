<div class="py-4 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div
            class="bg-white shadow-sm rounded-lg mb-6 p-4 border-l-4 border-indigo-600 flex justify-between items-center sticky top-2 z-10">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Ruta de Hoy</h2>
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
            </div>
            <div class="text-right">
                <span class="block text-xs font-semibold text-gray-400 uppercase">En Caja</span>
                <span class="block text-xl font-bold text-green-600">$ {{ number_format($collectedToday, 0) }}</span>
            </div>
        </div>

        @if ($groupedClients->isEmpty())
            <div class="bg-white rounded-lg p-8 text-center border-2 border-dashed border-gray-300">
                <div
                    class="mx-auto h-12 w-12 text-green-400 flex items-center justify-center rounded-full bg-green-50 mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900">¡Todo limpio!</h3>
                <p class="mt-1 text-sm text-gray-500">No hay cuotas pendientes ni vencidas para hoy.</p>
            </div>
        @else
            <div class="space-y-8">
                @foreach ($groupedClients as $group)
                    <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200">

                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $group['client']->full_name }}</h3>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($group['client']->address) }}"
                                    target="_blank" class="text-xs text-indigo-600 flex items-center hover:underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $group['client']->address }}
                                </a>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500 uppercase">Total a Cobrar</span>
                                <span class="block text-lg font-bold text-gray-800">$
                                    {{ number_format($group['total_due'], 0) }}</span>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach ($group['installments'] as $installment)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">

                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="text-sm font-bold text-gray-700">Cuota
                                                #{{ $installment->installment_number }}</span>

                                            @if ($installment->due_date < now()->today())
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 border border-red-200">
                                                    Venció {{ $installment->due_date->format('d/m') }}
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600 border border-blue-200">
                                                    Vence Hoy
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            @if ($installment->amount_paid > 0)
                                                Pagado parcial: ${{ number_format($installment->amount_paid, 0) }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <span class="font-bold text-gray-800 text-lg">
                                            $ {{ number_format($installment->amount - $installment->amount_paid, 0) }}
                                        </span>

                                        <a href="{{ route('collector.checkout', $installment->id) }}"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
