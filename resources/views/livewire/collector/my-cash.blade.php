<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-xl font-bold text-gray-800 mb-4">Mi Recaudación de Hoy</h2>

        <div class="bg-gray-900 rounded-lg p-6 text-white shadow-lg mb-6">
            <div class="text-center">
                <span class="text-gray-400 text-xs uppercase tracking-wider">Total en mi poder</span>
                <p class="text-4xl font-bold mt-1">$ {{ number_format($total, 0) }}</p>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-gray-700 pt-4">
                <div class="text-center border-r border-gray-700">
                    <span class="block text-xs text-gray-400">Efectivo</span>
                    <span class="block text-xl font-bold text-green-400">$ {{ number_format($cash, 0) }}</span>
                </div>
                <div class="text-center">
                    <span class="block text-xs text-gray-400">Transferencia</span>
                    <span class="block text-xl font-bold text-blue-400">$ {{ number_format($transfer, 0) }}</span>
                </div>
            </div>
        </div>

        <h3 class="font-medium text-gray-900 mb-3">Movimientos del Día</h3>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <ul class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <li class="p-4 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $payment->installment->credit->client->full_name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $payment->created_at->format('H:i') }} hs •
                                @if ($payment->payment_method->value === 'cash')
                                    Efectivo
                                @else
                                    Transferencia
                                @endif
                            </p>
                        </div>
                        <span class="text-sm font-bold text-gray-800">
                            + ${{ number_format($payment->amount, 0) }}
                        </span>
                    </li>
                @empty
                    <li class="p-8 text-center text-gray-400 text-sm">
                        Todavía no hiciste cobros hoy.
                    </li>
                @endforelse
            </ul>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('collector.dashboard') }}" class="text-indigo-600 font-medium text-sm">Volver a Hoja de
                Ruta</a>
        </div>
    </div>
</div>
