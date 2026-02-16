<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="transfer">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Migrar Cartera de Clientes
                            </h3>

                            @if ($sourceCollector)
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                                    <p class="text-sm text-blue-700">
                                        Origen: <strong>{{ $sourceCollector->name }}</strong>
                                    </p>
                                </div>
                            @endif

                            <div class="mb-4 border border-gray-200 rounded-md p-3">
                                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100">
                                    <span class="text-xs font-bold text-gray-500 uppercase">Créditos Disponibles</span>

                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectAll"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 font-bold">Seleccionar Todos</span>
                                    </label>
                                </div>

                                <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                                    @forelse($creditsList as $credit)
                                        <label
                                            class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer border border-transparent hover:border-gray-200 transition">

                                            <input type="checkbox" value="{{ $credit['id'] }}"
                                                wire:model.live="selectedCredits"
                                                class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm">

                                            <div class="text-sm w-full">
                                                <div class="flex justify-between">
                                                    <span class="font-bold text-gray-700">
                                                        {{ $credit['client_name'] }}
                                                    </span>

                                                    <span class="text-gray-600 font-mono">
                                                        ${{ number_format($credit['amount_pending'], 2, ',', '.') }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-400">Crédito #{{ $credit['id'] }}</p>
                                            </div>
                                        </label>
                                    @empty
                                        <p class="text-sm text-center text-gray-400 py-4">Este cobrador no tiene
                                            créditos activos.</p>
                                    @endforelse
                                </div>

                                <div class="mt-2 text-right">
                                    <span class="text-xs text-indigo-600 font-bold bg-indigo-50 px-2 py-1 rounded">
                                        {{ count($selectedCredits) }} seleccionados
                                    </span>
                                </div>
                                @error('selectedCredits')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Nuevo Responsable
                                    (Destino)</label>
                                <select wire:model="targetCollectorId"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">-- Elegir Cobrador --</option>
                                    @foreach ($targetCollectors as $tc)
                                        <option value="{{ $tc->id }}">{{ $tc->name }}</option>
                                    @endforeach
                                </select>
                                @error('targetCollectorId')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Confirmar Transferencia
                            </button>
                            <button type="button" wire:click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
