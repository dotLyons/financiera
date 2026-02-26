<div>
    @if ($isOpen)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    wire:click="close"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Editar Cuota #{{ $installment->installment_number ?? '' }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Modifica los detalles estructurales de la cuota.
                                </p>

                                <div class="grid grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase">Nueva
                                            Fecha</label>
                                        <input type="date" wire:model="newDate"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        @error('newDate')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase">Monto de
                                            Cuota</label>
                                        <input type="number" step="0.01" wire:model="newAmount"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        @error('newAmount')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- SECCIÓN DE PAGO (SOLO SI YA FUE COBRADA) --}}
                                @if ($paymentId)
                                    <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                                        <div class="flex items-center mb-3">
                                            <svg class="h-4 w-4 text-orange-500 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wide">
                                                Modificar Pago Registrado</h4>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600">Monto
                                                    Cobrado</label>
                                                <input type="number" step="0.01" wire:model="paymentAmount"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                @error('paymentAmount')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600">Método
                                                    Usado</label>
                                                <select wire:model="paymentMethod"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="cash">Efectivo</option>
                                                    <option value="transfer">Transferencia</option>
                                                </select>
                                                @error('paymentMethod')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                        <button wire:click="save" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Guardar Cambios
                        </button>
                        <button wire:click="close" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Cancelar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
