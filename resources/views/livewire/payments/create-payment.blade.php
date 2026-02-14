<div>
    @if ($isOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    wire:click="close"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">

                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Registrar Cobro - Cuota #{{ $installment->installment_number }}
                                </h3>

                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-2">
                                        Cliente: <strong>{{ $installment->credit->client->full_name }}</strong>
                                    </p>

                                    <div
                                        class="p-3 bg-gray-50 rounded-md border border-gray-200 flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-500 uppercase">Saldo tras el
                                            pago:</span>

                                        @php
                                            $balance = $this->calculatedBalance;
                                        @endphp

                                        <span
                                            class="text-lg font-bold
                                            {{ $balance == 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-800') }}">

                                            @if ($balance < -0.01)
                                                Excede por ${{ number_format(abs($balance), 2) }}
                                            @elseif(abs($balance) < 0.01)
                                                $ 0.00 (Pagado Total)
                                            @else
                                                $ {{ number_format($balance, 2) }} (Pendiente)
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-4">

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Monto ($)</label>
                                            <input type="number" wire:model.live="amount"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            @error('amount')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Forma de Pago</label>
                                            <select wire:model="method"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                @foreach ($paymentMethods as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex items-center mt-2">
                                        <input id="mixed_payment" type="checkbox" wire:model.live="isMixed"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="mixed_payment" class="ml-2 block text-sm text-gray-900">
                                            Pago Mixto (Combinar dos métodos)
                                        </label>
                                    </div>

                                    @if ($isMixed)
                                        <div class="p-3 bg-gray-50 rounded-md border border-gray-200 mt-2">
                                            <p class="text-xs text-indigo-600 font-bold mb-2 uppercase">Segundo Método
                                            </p>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Monto
                                                        ($)</label>
                                                    <input type="number" wire:model.live="secondAmount"
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    @error('secondAmount')
                                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Forma de
                                                        Pago</label>
                                                    <select wire:model="secondMethod"
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        @foreach ($paymentMethods as $key => $label)
                                                            <option value="{{ $key }}">{{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('secondMethod')
                                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="save" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmar Cobro e Imprimir
                        </button>
                        <button wire:click="close" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <script>
            window.addEventListener('open-pdf', event => {
                window.open(event.detail.url, '_blank');
            });
        </script>
    @endif
</div>
