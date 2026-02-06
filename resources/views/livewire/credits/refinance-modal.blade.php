<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('isOpen', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            Refinanciar Saldo Pendiente
                        </h3>

                        <div class="bg-red-50 p-4 rounded-md border border-red-100 mb-6">
                            <span class="block text-sm text-red-600 font-bold uppercase">Deuda Actual (Capital
                                Base)</span>
                            <span class="block text-3xl font-bold text-red-800">$
                                {{ number_format($outstandingBalance, 2) }}</span>
                            <p class="text-xs text-red-500 mt-1">Este monto será la base para el nuevo cálculo. Las
                                cuotas anteriores se eliminarán.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Interés Refinanciación
                                    (%)</label>
                                <input type="number" wire:model.live="newInterest"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Nuevas Cuotas</label>
                                <input type="number" wire:model.live="newInstallments"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Frecuencia</label>
                                <select wire:model.live="newFrequency"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="daily">Diario</option>
                                    <option value="weekly">Semanal</option>
                                    <option value="biweekly">Quincenal</option>
                                    <option value="monthly">Mensual</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Primer Vencimiento</label>
                                <input type="date" wire:model="startDate"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="bg-indigo-50 p-4 rounded-md border border-indigo-100 mt-2">
                            <h4 class="text-xs font-bold text-indigo-500 uppercase mb-2">Simulación del Nuevo Plan</h4>
                            <div class="flex justify-between items-end">
                                <div>
                                    <span class="text-sm text-gray-600">Nuevo Total a Devolver:</span>
                                    <div class="font-bold text-gray-800">$ {{ number_format($newTotal, 2) }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm text-gray-600">Valor Cuota Aprox:</span>
                                    <div class="font-bold text-indigo-700 text-xl">$
                                        {{ number_format($newInstallmentAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        @error('general')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="refinance"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmar Refinanciación
                        </button>
                        <button type="button" wire:click="$set('isOpen', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
