<div class="py-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-xl font-bold text-gray-800 mb-4">Mi Recaudación de Hoy</h2>

        <div class="bg-gray-900 rounded-xl p-6 text-white shadow-lg mb-4">
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

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 mb-8">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Mi Meta Mensual</h3>

            @php
                // Lógica de Semáforo
                if ($monthlyGoalPercent >= 100) {
                    $barColor = 'bg-yellow-400';
                    $textColor = 'text-yellow-600';
                    $msg = '¡Meta Cumplida!';
                } elseif ($monthlyGoalPercent >= 80) {
                    $barColor = 'bg-green-500';
                    $textColor = 'text-green-600';
                    $msg = 'Excelente';
                } elseif ($monthlyGoalPercent >= 50) {
                    $barColor = 'bg-yellow-500';
                    $textColor = 'text-yellow-600';
                    $msg = 'En camino';
                } else {
                    $barColor = 'bg-red-500';
                    $textColor = 'text-red-600';
                    $msg = 'Bajo rendimiento';
                }
            @endphp

            <div class="flex justify-between items-end mb-2">
                <span class="text-xs text-gray-500">{{ $msg }}</span>
                <span class="text-lg font-bold {{ $textColor }}">{{ number_format($monthlyGoalPercent, 1) }}%</span>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500"
                    style="width: {{ $monthlyGoalPercent > 100 ? 100 : $monthlyGoalPercent }}%">
                </div>
            </div>
        </div>

        <h3 class="font-medium text-gray-900 mb-3 pl-1">Movimientos del Día</h3>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
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
                        <div class="flex justify-center mb-2">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        Todavía no hiciste cobros hoy.
                    </li>
                @endforelse
            </ul>
        </div>

        <div class="mt-8 text-center pb-6">
            <a href="{{ route('collector.dashboard') }}" class="text-indigo-600 font-medium text-sm hover:underline">
                &larr; Volver a Hoja de Ruta
            </a>
        </div>
    </div>
</div>
