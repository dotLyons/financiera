<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <div class="mb-6 border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Regularización de Pagos Históricos</h2>
                <p class="text-gray-600 mt-1">
                    El crédito para <strong>{{ $credit->client->name }}</strong> inició en el pasado.
                    Por favor, indique qué cuotas ya fueron abonadas antes de ingresar al sistema.
                </p>
                <div class="mt-2 text-sm text-yellow-600 bg-yellow-50 p-2 rounded border border-yellow-200">
                    ⚠️ <strong>Nota:</strong> Estos pagos se registrarán como "Migración" y <strong>NO afectarán la caja
                        diaria de hoy</strong>.
                </div>
            </div>

            <form wire:submit.prevent="process">
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Total
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase bg-green-50">
                                    ¿Pagó Todo?</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase bg-blue-50">
                                    ¿Pago Parcial?</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($installments as $inst)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $inst->number }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 font-bold">
                                        ${{ number_format($inst->amount, 2, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-center bg-green-50/30">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" value="{{ $inst->id }}" wire:model="selectedFull"
                                                class="form-checkbox h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500 cursor-pointer">
                                        </label>
                                    </td>

                                    <td class="px-4 py-3 text-right bg-blue-50/30">
                                        @if (!in_array($inst->id, $selectedFull))
                                            <div class="relative rounded-md shadow-sm">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-xs">$</span>
                                                </div>
                                                <input type="number" step="0.01"
                                                    wire:model="partialAmounts.{{ $inst->id }}"
                                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-6 sm:text-sm border-gray-300 rounded-md text-right"
                                                    placeholder="0.00">
                                            </div>
                                        @else
                                            <span class="text-xs text-green-600 font-bold">Pago Completo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                        No hay cuotas vencidas anteriores a la fecha.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('credits.index') }}"
                        class="text-gray-600 hover:text-gray-900 text-sm font-medium underline">
                        Omitir (Dejar todo como impago)
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Confirmar Historial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
