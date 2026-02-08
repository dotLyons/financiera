<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                    Gestión de Créditos
                </h2>
                <p class="text-sm text-gray-500 mt-1">Administre los préstamos otorgados y monitoree su cobro.</p>
            </div>

            <a href="{{ route('credits.create') }}"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-5 rounded-md shadow-sm transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Otorgar Crédito
            </a>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-4 mb-6">
            <div class="grid grid-cols-12 gap-4 items-end">

                <div class="col-span-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        Buscar Cliente
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            class="pl-10 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md sm:text-sm"
                            placeholder="Nombre, DNI o #ID">
                    </div>
                </div>

                <div class="col-span-3">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        Estado
                    </label>
                    <select wire:model.live="statusFilter"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md sm:text-sm">
                        <option value="">Todos los estados</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-3">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        Cobrador
                    </label>
                    <select wire:model.live="collectorFilter"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md sm:text-sm">
                        <option value="">Cualquiera</option>
                        @foreach ($collectors as $collector)
                            <option value="{{ $collector->id }}">{{ $collector->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 text-right pb-2">
                    <span class="text-sm font-medium text-gray-500">{{ $credits->total() }} registros</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref /
                            Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Detalles del Préstamo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cobrador</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Progreso</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Ver</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($credits as $credit)
                        @php
                            // Cálculos rápidos visuales
                            $totalPaid = $credit->installments->sum('amount_paid');
                            $percentage = $credit->amount_total > 0 ? ($totalPaid / $credit->amount_total) * 100 : 0;

                            $badgeColor = match ($credit->status->value) {
                                'paid' => 'bg-green-100 text-green-800',
                                'defaulted' => 'bg-red-100 text-red-800',
                                default => 'bg-indigo-100 text-indigo-800',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-indigo-600">#{{ $credit->id }}</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $credit->client->full_name }}</span>
                                    <span class="text-xs text-gray-500">{{ $credit->client->dni }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-bold">$
                                    {{ number_format($credit->amount_total, 2) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $credit->installments_count }} cuotas de
                                    @switch($credit->payment_frequency)
                                        @case('daily')
                                            Diario
                                        @break

                                        @case('weekly')
                                            Semanal
                                        @break

                                        @case('biweekly')
                                            Quincenal
                                        @break

                                        @case('monthly')
                                            Mensual
                                        @break

                                        @default
                                            {{ $credit->payment_frequency }}
                                    @endswitch
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-2">
                                        {{ substr($credit->collector->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $credit->collector->name }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 align-middle">
                                <div class="w-full max-w-xs mx-auto">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-600">{{ number_format($percentage, 0) }}%</span>
                                        <span class="text-gray-500">${{ number_format($totalPaid, 0) }} pagado</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $credit->status->value == 'paid' ? 'bg-green-500' : 'bg-indigo-600' }}"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                    {{ strtoupper($credit->status->label()) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('clients.history', $credit->client_id) }}"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <span>No se encontraron créditos con esos filtros.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $credits->links() }}
            </div>
        </div>
    </div>
