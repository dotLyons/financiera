<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('collector.dashboard') }}" class="inline-flex items-center text-sm text-gray-500 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Cancelar y Volver
        </a>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-indigo-500">
            <div class="p-6">

                <div class="text-center mb-6">
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Cobrando a</span>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $installment->credit->client->full_name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $installment->credit->client->address }}</p>
                </div>

                <div class="bg-indigo-50 rounded-lg p-4 mb-6 text-center">
                    <p class="text-sm text-indigo-600 mb-1">Cuota #{{ $installment->installment_number }}</p>
                    <p class="text-3xl font-bold text-indigo-700">$
                        {{ number_format($installment->amount - $installment->amount_paid, 0) }}</p>
                    <p class="text-xs text-indigo-400">Saldo Pendiente</p>
                </div>

                <form wire:submit.prevent="processPayment">

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto que recibes ($)</label>
                        <input type="number" inputmode="decimal" step="0.01" wire:model="amount"
                            class="block w-full text-center text-3xl font-bold border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-4"
                            placeholder="0.00">
                        @error('amount')
                            <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pago</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="cash" class="peer sr-only">
                                <div
                                    class="rounded-lg border border-gray-200 p-4 text-center hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition">
                                    <span class="block text-lg font-bold">💵 Efectivo</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="transfer" class="peer sr-only">
                                <div
                                    class="rounded-lg border border-gray-200 p-4 text-center hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition">
                                    <span class="block text-lg font-bold">📱 Transf.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-4 px-4 border border-transparent rounded-md shadow-sm text-lg font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                        CONFIRMAR COBRO
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>
