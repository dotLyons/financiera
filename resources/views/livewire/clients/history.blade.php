<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('clients.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al listado de clientes
            </a>

            <a href="{{ route('report.client', $client->id) }}" target="_blank"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Descargar Resumen Global
            </a>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center">

                <div class="flex items-center space-x-5">
                    <div
                        class="h-16 w-16 bg-indigo-50 rounded-full flex items-center justify-center border border-indigo-100 text-indigo-600 text-xl font-bold flex-shrink-0">
                        {{ substr($client->first_name, 0, 1) }}{{ substr($client->last_name, 0, 1) }}
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                            {{ $client->full_name }}
                        </h2>

                        <p class="text-xs text-indigo-600 font-bold uppercase tracking-wide mb-2">
                            {{ $client->rubro ?? 'Sin Rubro' }}
                        </p>

                        @php
                            $scoreColors = [
                                0 => 'bg-gray-100 text-gray-600 border-gray-200',
                                1 => 'bg-green-100 text-green-700 border-green-200',
                                2 => 'bg-blue-100 text-blue-700 border-blue-200',
                                3 => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                4 => 'bg-orange-100 text-orange-700 border-orange-200',
                                5 => 'bg-red-100 text-red-700 border-red-200',
                            ];
                            $scoreLabels = [
                                0 => 'NUEVO / SIN HISTORIAL',
                                1 => 'NIVEL 1: EXCELENTE',
                                2 => 'NIVEL 2: BUENO',
                                3 => 'NIVEL 3: REGULAR',
                                4 => 'NIVEL 4: RIESGOSO',
                                5 => 'NIVEL 5: INCOBRABLE',
                            ];
                            $sColor = $scoreColors[$client->credit_score] ?? $scoreColors[0];
                            $sLabel = $scoreLabels[$client->credit_score] ?? $scoreLabels[0];
                        @endphp

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-3">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black border {{ $sColor }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                                {{ $sLabel }}
                            </span>
                            @if ($client->credit_score_notes)
                                <span
                                    class="text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                    "{{ $client->credit_score_notes }}"
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-col text-sm text-gray-500 space-y-1">

                            <div class="flex items-center space-x-3">
                                <span class="flex items-center" title="DNI / CUIT">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .883-.393 1.627-1.08 1.998">
                                        </path>
                                    </svg>
                                    {{ $client->dni }}
                                </span>

                                <span class="text-gray-300">|</span>

                                <span class="flex items-center" title="Teléfonos">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                    {{ $client->phone ?? '-' }}
                                    @if ($client->reference_phone)
                                        <span class="text-gray-400 text-xs ml-2">(Ref:
                                            {{ $client->reference_phone }})</span>
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center" title="Domicilio de Cobro">
                                <svg class="w-4 h-4 mr-1 text-gray-400 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $client->address }}</span>
                            </div>

                            @if ($client->second_address)
                                <div class="flex items-center text-gray-400" title="Domicilio Alternativo / Laboral">
                                    <svg class="w-4 h-4 mr-1 text-gray-300 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    <span class="text-xs">{{ $client->second_address }}</span>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-8">
                    <div class="text-right">
                        <span
                            class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Historial</span>
                        <span class="block text-2xl font-bold text-gray-800">{{ $stats['total_credits'] }} <span
                                class="text-sm font-normal text-gray-500">créditos</span></span>
                    </div>

                    <div class="h-10 w-px bg-gray-200"></div>

                    <div class="text-right">
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Deuda
                            Activa</span>
                        <span
                            class="block text-3xl font-bold {{ $stats['total_debt'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                            $ {{ number_format($stats['total_debt'], 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-medium text-gray-900 mb-4 px-1">Detalle de Operaciones</h3>

        <div class="space-y-4">
            @forelse($credits as $credit)
                @php
                    $totalPaid = $credit->installments->sum('amount_paid');
                    $totalAmount = $credit->amount_total;

                    $totalPunitory = $credit->installments->sum('punitory_interest');
                    $totalPunitoryPaid = $credit->installments->sum('punitory_paid');

                    $progress = $totalAmount > 0 ? ($totalPaid / $totalAmount) * 100 : 0;
                    $isPaid = $credit->status->value === 'paid';
                    $isDefaulted = $credit->status->value === 'defaulted';

                    $borderColor = $isPaid
                        ? 'border-green-500'
                        : ($isDefaulted
                            ? 'border-red-500'
                            : 'border-indigo-500');
                    $badgeClass = $isPaid
                        ? 'bg-green-100 text-green-800 border-green-200'
                        : ($isDefaulted
                            ? 'bg-red-100 text-red-800 border-red-200'
                            : 'bg-indigo-100 text-indigo-800 border-indigo-200');
                    $progressColor = $isPaid ? 'bg-green-500' : 'bg-indigo-600';
                @endphp

                <div x-data="{ open: false }"
                    class="bg-white border border-gray-200 border-l-4 {{ $borderColor }} shadow-sm rounded-r-lg overflow-hidden transition hover:shadow-md">

                    <div @click="open = !open" class="grid grid-cols-12 gap-4 p-5 cursor-pointer items-center group">

                        <div class="col-span-3">
                            <div class="flex items-center space-x-3">
                                <span
                                    class="text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition">#{{ $credit->id }}</span>
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                    {{ strtoupper($credit->status->label()) }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Creado el {{ $credit->date_of_award->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="col-span-4 border-l border-gray-100 pl-4">
                            <div class="text-sm text-gray-900 font-medium">
                                @switch($credit->payment_frequency)
                                    @case('daily')
                                        Diario
                                    @break

                                    @case('weekly')
                                        Semanal
                                    @break

                                    @case('biweekly')
                                        Quincenal
                                    @break

                                    @case('monthly')
                                        Mensual
                                    @break

                                    @default
                                        {{ $credit->payment_frequency }}
                                @endswitch
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $credit->installments_count }} cuotas desde
                                {{ $credit->start_date->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="col-span-4 border-l border-gray-100 pl-4 pr-4">
                            <div class="flex justify-between text-xs font-semibold uppercase text-gray-500 mb-1">
                                <span>Progreso de Capital</span>
                                <span>${{ number_format($totalPaid, 0) }} /
                                    ${{ number_format($totalAmount, 0) }}</span>
                            </div>

                            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                                <div class="h-2 rounded-full {{ $progressColor }}"
                                    style="width: {{ min($progress, 100) }}%"></div>
                            </div>

                            @if ($totalPunitory > 0)
                                <div class="flex justify-end items-center mt-1">
                                    <span
                                        class="text-[10px] uppercase font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-100"
                                        title="Interés generado por atrasos en este crédito">
                                        MORA: ${{ number_format($totalPunitoryPaid, 0) }} Cobrados /
                                        ${{ number_format($totalPunitory, 0) }} Total
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="col-span-1 flex justify-end">
                            <div
                                class="h-8 w-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-indigo-50 transition">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="bg-gray-50 border-t border-gray-200">
                        <div class="p-4">

                            <div class="flex justify-end mb-4 space-x-3">

                                @if ($credit->status->value === 'active' || $credit->status->value === 'defaulted')
                                    <a href="{{ route('credits.edit', $credit) }}"
                                        class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 border border-yellow-300 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-yellow-200 transition mr-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Corregir
                                    </a>
                                @endif

                                @if ($credit->status->value === 'active' && $credit->installments->where('status', '!=', 'paid')->count() > 0)
                                    <button
                                        wire:click="$dispatch('openRefinanceModal', { creditId: {{ $credit->id }} })"
                                        class="inline-flex items-center px-3 py-2 bg-orange-100 text-orange-700 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-orange-200 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        Refinanciar Saldo
                                    </button>
                                @endif

                                <a href="{{ route('contract.new', $credit->id) }}" target="_blank"
                                    class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Contrato / Pagaré
                                </a>

                                @if ($credit->is_refinanced)
                                    <a href="{{ route('contract.refinance', $credit->id) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Constancia Refin.
                                    </a>
                                @endif

                                <a href="{{ route('report.credit', $credit->id) }}" target="_blank"
                                    class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Descargar Detalle PDF
                                </a>
                            </div>

                            <table
                                class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cuota</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vencimiento</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Monto Original</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pagado</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($credit->installments as $installment)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $installment->installment_number }}
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center space-x-2">
                                                    <span
                                                        class="{{ $installment->due_date < now() && $installment->status->value != 'paid' ? 'text-red-600 font-bold' : '' }}">
                                                        {{ $installment->due_date->format('d/m/Y') }}
                                                    </span>

                                                    {{-- LÁPIZ DE EDICIÓN SIEMPRE VISIBLE --}}
                                                    <button
                                                        wire:click="$dispatch('openEditDateModal', { installmentId: {{ $installment->id }} })"
                                                        class="text-gray-400 hover:text-indigo-600 transition"
                                                        title="Editar Cuota / Pago">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>

                                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 text-right">
                                                ${{ number_format($installment->amount, 2) }}
                                                @if ($installment->punitory_interest > 0)
                                                    <span class="block text-[10px] text-red-500 font-bold mt-0.5">
                                                        +${{ number_format($installment->punitory_interest, 2) }} mora
                                                    </span>
                                                @endif
                                            </td>

                                            <td
                                                class="px-6 py-3 whitespace-nowrap text-sm text-green-600 font-medium text-right">
                                                ${{ number_format($installment->amount_paid, 2) }}
                                                @if ($installment->punitory_paid > 0)
                                                    <span class="block text-[10px] text-orange-400 font-bold mt-0.5"
                                                        title="Mora pagada">
                                                        +${{ number_format($installment->punitory_paid, 2) }} mora
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                                @if ($installment->status->value == 'paid')
                                                    {{-- BOTÓN PAGADO AHORA ES CLICKEABLE --}}
                                                    <button
                                                        wire:click="$dispatch('openEditDateModal', { installmentId: {{ $installment->id }} })"
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 hover:bg-green-200 hover:shadow-sm transition cursor-pointer"
                                                        title="Editar datos del pago">
                                                        ✔ Pagado
                                                        <svg class="w-3 h-3 ml-1 opacity-70" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <button
                                                            wire:click="$dispatch('openPaymentModal', { installmentId: {{ $installment->id }} })"
                                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-bold rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                                            $ Cobrar
                                                        </button>

                                                        @if ($installment->status->value == 'partial')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200"
                                                                title="Pago parcial">
                                                                Resta:
                                                                ${{ number_format($installment->amount - $installment->amount_paid, 0) }}
                                                            </span>
                                                        @elseif($installment->status->value == 'overdue')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600 border border-red-200">
                                                                Vencida
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Sin historial</h3>
                        <p class="mt-1 text-sm text-gray-500">Este cliente aún no tiene créditos registrados.</p>
                    </div>
                @endforelse
            </div>

            @livewire('payments.create-payment')
            @livewire('credits.refinance-modal')
            @livewire('installments.edit-date')
        </div>
    </div>
