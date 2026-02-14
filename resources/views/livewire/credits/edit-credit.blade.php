<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-red-700 tracking-tight flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Corrección de Crédito #{{ $credit->id }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Modifique los valores. El plan de pagos se regenerará
                    completamente.</p>
            </div>

            <a href="{{ route('clients.history', $credit->client_id) }}"
                class="text-sm text-gray-500 hover:text-indigo-600 underline decoration-gray-300 underline-offset-4 transition">
                Cancelar y Volver
            </a>
        </div>

        <div class="bg-white border border-red-100 shadow-sm rounded-lg overflow-hidden">
            <form wire:submit.prevent="update">
                <div class="p-8 space-y-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="client_id" value="Cliente" />
                            <select wire:model="client_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="client_id" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="collector_id" value="Cobrador" />
                            <select wire:model="collector_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach ($collectors as $collector)
                                    <option value="{{ $collector->id }}">{{ $collector->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-label for="amount_net" value="Monto ($)" />
                            <input type="number" wire:model.live="amount_net"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                            <x-input-error for="amount_net" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="interest_rate" value="Interés (%)" />
                            <input type="number" wire:model.live="interest_rate"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <x-label for="installments_count" value="Cuotas" />
                            <input type="number" wire:model.live="installments_count"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-label for="payment_frequency" value="Frecuencia" />
                            <select wire:model="payment_frequency"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                @foreach ($frequencies as $freq)
                                    <option value="{{ $freq->value }}">{{ $freq->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label for="date_of_award" value="Fecha Otorgamiento" />
                            <input type="date" wire:model="date_of_award"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <x-label for="start_date" value="Fecha 1er Pago" />
                            <input type="date" wire:model="start_date"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mt-6">
                        <h3 class="text-red-800 font-bold text-sm uppercase mb-2">Zona de Auditoría (Obligatorio)</h3>
                        <p class="text-xs text-red-600 mb-3">
                            Atención: Al guardar, se <strong>eliminarán</strong> las cuotas actuales y se regenerarán
                            nuevas.
                        </p>

                        <x-label for="edition_reason" value="Motivo de la corrección" />
                        <textarea wire:model="edition_reason" rows="2"
                            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Ej: Error al ingresar la tasa de interés..."></textarea>
                        <x-input-error for="edition_reason" class="mt-2" />

                        @error('base')
                            <div class="mt-2 text-red-600 font-bold text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="bg-gray-50 px-8 py-4 flex items-center justify-end border-t border-gray-200">
                    <x-button class="bg-red-600 hover:bg-red-700">
                        Confirmar Corrección
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
