<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Otorgar Nuevo Crédito</h2>
                <p class="text-sm text-gray-500 mt-1">Configure los términos del préstamo. El sistema generará el plan de
                    pagos automáticamente.</p>
            </div>
            <a href="{{ route('credits.index') }}"
                class="text-sm text-gray-500 hover:text-indigo-600 underline decoration-gray-300 underline-offset-4 transition">
                Volver al listado
            </a>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <form wire:submit.prevent="save">

                <div class="p-8 space-y-8">

                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Asignación
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <x-label for="client_id" value="Cliente Solicitante" />
                                <div class="relative mt-1">
                                    <select id="client_id" wire:model.live="client_id"
                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                        <option value="">Seleccione un cliente...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->full_name }}
                                                ({{ $client->dni }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error for="client_id" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="collector_id" value="Cobrador Responsable (Actual)" />
                                <div class="relative mt-1">
                                    <select id="collector_id" wire:model="collector_id"
                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                        <option value="">Seleccione un cobrador...</option>
                                        @foreach ($collectors as $collector)
                                            <option value="{{ $collector->id }}">{{ $collector->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error for="collector_id" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Condiciones del Préstamo
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>
                                <x-label for="amount_net" value="Monto a Entregar ($)" />
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" wire:model.live="amount_net" id="amount_net"
                                        class="block w-full rounded-md border-gray-300 pl-7 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="0.00">
                                </div>
                                <x-input-error for="amount_net" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="interest_rate" value="Tasa de Interés (%)" />
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <input type="number" step="0.01" wire:model.live="interest_rate"
                                        id="interest_rate"
                                        class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="20">
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                                <x-input-error for="interest_rate" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="date_of_award" value="Fecha de Otorgamiento" />
                                <x-input id="date_of_award" type="date" class="mt-1 block w-full"
                                    wire:model="date_of_award" />
                                <p class="mt-1 text-xs text-gray-500">Fecha real de entrega del dinero.</p>
                                <x-input-error for="date_of_award" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="start_date" value="Fecha Primer Pago" />
                                <x-input id="start_date" type="date" class="mt-1 block w-full"
                                    wire:model="start_date" />
                                <x-input-error for="start_date" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-label for="installments_count" value="Cantidad de Cuotas" />
                                <x-input id="installments_count" type="number" class="mt-1 block w-full"
                                    wire:model.live="installments_count" />
                                <x-input-error for="installments_count" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="payment_frequency" value="Frecuencia de Pago" />
                                <select id="payment_frequency" wire:model="payment_frequency"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
                                    @foreach ($frequencies as $freq)
                                        <option value="{{ $freq->value }}">{{ $freq->label() }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="payment_frequency" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4
                                class="text-sm font-semibold text-yellow-800 uppercase tracking-wider mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Opciones de Migración / Crédito Preexistente
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        ¿Desde qué cuota se empieza a cobrar?
                                    </label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span
                                            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            #
                                        </span>
                                        <input type="number" wire:model.live="start_installment" min="1"
                                            max="{{ $installments_count }}"
                                            class="focus:ring-yellow-500 focus:border-yellow-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Dejar en <b>1</b> para créditos nuevos. Si pones <b>6</b>, las primeras 5 se
                                        marcarán como pagadas.
                                    </p>
                                    <x-input-error for="start_installment" class="mt-2" />
                                </div>

                                @if ($start_installment > 1)
                                    <div>
                                        <label class="block text-sm font-medium text-yellow-800">
                                            ¿Quién cobró las cuotas anteriores?
                                        </label>
                                        <select wire:model="historical_collector_id"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                            <option value="">-- Seleccionar Cobrador --</option>
                                            @foreach ($collectors as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-yellow-600">
                                            * Solo para historial. No afectará la caja actual.
                                        </p>
                                        <x-input-error for="historical_collector_id" class="mt-2" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($amount_net && $interest_rate && $installments_count)
                        <div class="mt-8 rounded-lg bg-indigo-50 border border-indigo-100 p-6">
                            <h4 class="text-sm font-semibold text-indigo-900 uppercase tracking-wider mb-4">Resumen de
                                la Operación</h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
                                <div>
                                    <span class="block text-xs text-indigo-500">Monto Total a Devolver</span>
                                    <span class="block text-3xl font-bold text-indigo-700">
                                        ${{ number_format($amount_net * (1 + $interest_rate / 100), 2) }}
                                    </span>
                                    <span class="text-xs text-indigo-400">Capital + Interés</span>
                                </div>

                                <div>
                                    <span class="block text-xs text-indigo-500">Valor Estimado por Cuota</span>
                                    <span class="block text-3xl font-bold text-indigo-700">
                                        ${{ number_format(($amount_net * (1 + $interest_rate / 100)) / $installments_count, 2) }}
                                    </span>
                                    <span class="text-xs text-indigo-400">Fijo x {{ $installments_count }}
                                        cuotas</span>
                                </div>

                                <div>
                                    <span class="block text-xs text-indigo-500">Ganancia Esperada (Interés)</span>
                                    <span class="block text-xl font-bold text-green-600 mt-2">
                                        +
                                        ${{ number_format($amount_net * (1 + $interest_rate / 100) - $amount_net, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="bg-gray-50 px-8 py-4 flex items-center justify-end border-t border-gray-200">
                    <a href="{{ route('credits.index') }}"
                        class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900 underline decoration-gray-300 underline-offset-4 transition">
                        Cancelar
                    </a>

                    <x-button wire:loading.attr="disabled" class="ml-3">
                        {{ __('Confirmar y Generar Cuotas') }}
                    </x-button>
                </div>

            </form>
        </div>
    </div>
</div>
