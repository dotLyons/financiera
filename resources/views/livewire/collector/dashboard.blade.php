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
                    class="mx-auto h-12 w-12 text-gray-400 flex items-center justify-center rounded-full bg-gray-50 mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900">No hay clientes asignados</h3>
                <p class="mt-1 text-sm text-gray-500">Actualmente no tienes créditos activos en tu cartera.</p>
            </div>
        @else
            <div class="space-y-8">
                @foreach ($groupedClients as $group)
                    <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200">

                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 pr-3">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $group['client']->full_name }}
                                        </h3>

                                        @if ($group['is_advance'])
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                Al día
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">

                                        <div class="space-y-1.5">
                                            @if ($group['client']->address)
                                                <a href="https://maps.google.com/?q={{ urlencode($group['client']->address) }}"
                                                    target="_blank"
                                                    class="text-xs text-indigo-600 flex items-start hover:underline">
                                                    <svg class="w-3.5 h-3.5 mr-1 mt-0.5 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                    </svg>
                                                    <span class="leading-tight">{{ $group['client']->address }}</span>
                                                </a>
                                            @endif

                                            @if ($group['client']->phone)
                                                <a href="tel:{{ $group['client']->phone }}"
                                                    class="text-xs text-indigo-600 flex items-center hover:underline">
                                                    <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                        </path>
                                                    </svg>
                                                    {{ $group['client']->phone }}
                                                </a>
                                            @endif
                                        </div>

                                        <div
                                            class="space-y-1.5 pt-2 sm:pt-0 border-t border-gray-200 sm:border-t-0 sm:border-l sm:border-gray-200 sm:pl-3">
                                            @if ($group['client']->second_address)
                                                <a href="https://maps.google.com/?q={{ urlencode($group['client']->second_address) }}"
                                                    target="_blank"
                                                    class="text-xs text-orange-600 flex items-start hover:underline">
                                                    <svg class="w-3.5 h-3.5 mr-1 mt-0.5 text-orange-500 flex-shrink-0"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                    </svg>
                                                    <span class="leading-tight">Ref:
                                                        {{ $group['client']->second_address }}</span>
                                                </a>
                                            @endif

                                            @if ($group['client']->reference_phone)
                                                <a href="tel:{{ $group['client']->reference_phone }}"
                                                    class="text-xs text-orange-600 flex items-center hover:underline">
                                                    <svg class="w-3.5 h-3.5 mr-1 text-orange-500 flex-shrink-0"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                        </path>
                                                    </svg>
                                                    Ref: {{ $group['client']->reference_phone }}
                                                </a>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <span
                                        class="text-xs text-gray-500 uppercase">{{ $group['is_advance'] ? 'Próxima Cuota' : 'Total a Cobrar' }}</span>
                                    <span
                                        class="block text-lg font-bold {{ $group['is_advance'] ? 'text-gray-400' : 'text-gray-800' }}">
                                        $ {{ number_format($group['total_due'], 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($group['is_advance'])
                            <div class="p-6 bg-white flex flex-col items-center justify-center text-center">
                                <div class="rounded-full bg-green-50 p-3 mb-2">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 mb-4">No hay vencimientos pendientes ni atrasos.</p>

                                @if ($group['installments']->isNotEmpty())
                                    <a href="{{ route('collector.checkout', $group['installments']->first()->id) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Adelantar Cuota #{{ $group['installments']->first()->installment_number }}
                                    </a>
                                @else
                                    <p class="text-xs text-gray-400 font-bold uppercase">Crédito Finalizado</p>
                                @endif
                            </div>
                        @else
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
                                                $
                                                {{ number_format($installment->amount - $installment->amount_paid, 0) }}
                                            </span>

                                            <a href="{{ route('collector.checkout', $installment->id) }}"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </a>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
