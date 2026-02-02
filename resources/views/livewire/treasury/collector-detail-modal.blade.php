<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">

                    <div class="bg-gray-100 px-4 pt-5 pb-4 sm:p-6">

                        <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Hoja de Ruta: <span class="text-indigo-600">{{ $collector->name }}</span>
                                </h3>
                                <p class="text-sm text-gray-500">Detalle de operaciones del
                                    {{ \Carbon\Carbon::today()->format('d/m/Y') }}</p>
                            </div>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                <span class="text-3xl">&times;</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                                <span class="block text-xs font-bold text-gray-400 uppercase">Efectivo</span>
                                <span class="block text-2xl font-bold text-gray-800">$
                                    {{ number_format($cashInHand, 0) }}</span>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                                <span class="block text-xs font-bold text-gray-400 uppercase">Transferencias</span>
                                <span class="block text-2xl font-bold text-gray-800">$
                                    {{ number_format($transfersInHand, 0) }}</span>
                            </div>
                            <div class="bg-gray-800 p-4 rounded-lg shadow-sm text-white">
                                <span class="block text-xs font-bold text-gray-400 uppercase">Total Recaudado</span>
                                <span class="block text-2xl font-bold">$
                                    {{ number_format($totalCollectedToday, 0) }}</span>
                            </div>
                        </div>

                        <h4 class="text-sm font-bold text-gray-500 uppercase mb-3">Detalle de Visitas
                            ({{ $roadmap->count() }})</h4>

                        @if ($roadmap->isEmpty())
                            <div class="bg-white rounded-lg p-8 text-center border-2 border-dashed border-gray-300">
                                <p class="text-gray-500">No hay actividad registrada ni pendiente para este cobrador
                                    hoy.</p>
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach ($roadmap as $group)
                                    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">

                                        <div
                                            class="bg-white px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                                            <div>
                                                <h4 class="text-lg font-bold text-gray-900">
                                                    {{ $group['client']->full_name }}</h4>

                                                <div
                                                    class="flex flex-col sm:flex-row sm:items-center text-xs text-gray-500 mt-1 space-y-1 sm:space-y-0 sm:space-x-3">
                                                    <div class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                        </svg>
                                                        {{ $group['client']->address }}
                                                    </div>

                                                    @if ($group['client']->phone)
                                                        <div class="flex items-center">
                                                            <span class="hidden sm:inline text-gray-300 mx-2">|</span>
                                                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                                </path>
                                                            </svg>
                                                            <a href="tel:{{ $group['client']->phone }}"
                                                                class="hover:text-indigo-600 hover:underline">
                                                                {{ $group['client']->phone }}
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center">
                                                            <span class="hidden sm:inline text-gray-300 mx-2">|</span>
                                                            <span class="text-gray-400 italic">Sin teléfono</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right flex space-x-4">
                                                @if ($group['total_paid_today'] > 0)
                                                    <div>
                                                        <span
                                                            class="block text-[10px] uppercase font-bold text-gray-400">Cobrado</span>
                                                        <span class="block text-sm font-bold text-green-600">$
                                                            {{ number_format($group['total_paid_today'], 0) }}</span>
                                                    </div>
                                                @endif
                                                @if ($group['total_pending'] > 0)
                                                    <div>
                                                        <span
                                                            class="block text-[10px] uppercase font-bold text-gray-400">Resta</span>
                                                        <span class="block text-sm font-bold text-red-600">$
                                                            {{ number_format($group['total_pending'], 0) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="divide-y divide-gray-50">
                                            @foreach ($group['paid_items'] as $item)
                                                <div class="px-4 py-2 flex justify-between items-center bg-green-50/50">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-medium text-gray-700">Cuota
                                                            #{{ $item->installment_number }}</span>
                                                        <span
                                                            class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                            COBRADO HOY
                                                        </span>
                                                    </div>
                                                    <span class="font-bold text-gray-800 text-sm">
                                                        $
                                                        {{ number_format($item->payments->where('payment_date', '>=', \Carbon\Carbon::today())->sum('amount'), 0) }}
                                                    </span>
                                                </div>
                                            @endforeach

                                            @foreach ($group['pending_items'] as $item)
                                                <div
                                                    class="px-4 py-2 flex justify-between items-center hover:bg-gray-50">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-medium text-gray-700">Cuota
                                                            #{{ $item->installment_number }}</span>
                                                        @if ($item->due_date < now()->today())
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 border border-red-200">
                                                                Venció {{ $item->due_date->format('d/m') }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600 border border-blue-200">
                                                                Vence Hoy
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="font-bold text-gray-400 text-sm">
                                                        (Pend: $
                                                        {{ number_format($item->amount - $item->amount_paid, 0) }})
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="closeModal"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar Detalle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
