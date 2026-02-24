<!DOCTYPE html>
<html>

<head>
    <title>Hoja de Ruta</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 13px;
            color: #555;
        }

        .totals-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #f9f9f9;
        }

        .totals-box td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            font-size: 14px;
        }

        .client-section {
            margin-bottom: 15px;
            border: 1px solid #000;
            border-radius: 4px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .client-header {
            background: #eee;
            padding: 8px 10px;
            border-bottom: 1px solid #000;
        }

        .client-name {
            font-weight: bold;
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
            color: #111;
        }

        .client-info {
            font-size: 11px;
            color: #444;
            margin-top: 4px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items th,
        table.items td {
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        table.items th {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            background: #fafafa;
        }

        table.items td {
            font-size: 12px;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .status-paid {
            color: #16a34a;
            font-weight: bold;
        }

        .status-pending {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>HOJA DE RUTA DIARIA</h2>
        <p>Cobrador: <strong>{{ $user->name }}</strong> | Fecha: <strong>{{ $date->format('d/m/Y') }}</strong></p>
    </div>

    <table class="totals-box">
        <tr>
            <td>
                <span style="display:block; font-size: 10px; color: #666; text-transform:uppercase;">A Cobrar Hoy</span>
                <strong>$ {{ number_format($totalPending, 2) }}</strong>
            </td>
            <td>
                <span style="display:block; font-size: 10px; color: #666; text-transform:uppercase;">Ya Recaudado</span>
                <strong style="color: #16a34a;">$ {{ number_format($totalCollected, 2) }}</strong>
            </td>
            <td>
                <span style="display:block; font-size: 10px; color: #666; text-transform:uppercase;">Visitas</span>
                <strong>{{ count($roadmap) }} Clientes</strong>
            </td>
        </tr>
    </table>

    @forelse ($roadmap as $group)
        <div class="client-section">
            <div class="client-header">
                <p class="client-name">{{ $group['client']->full_name }}</p>
                <div class="client-info">
                    <strong>Dir:</strong> {{ $group['client']->address }}
                    @if ($group['client']->phone)
                        &nbsp;|&nbsp; <strong>Tel:</strong> {{ $group['client']->phone }}
                    @endif
                </div>
            </div>
            <table class="items">
                <thead>
                    <tr>
                        <th width="15%">Cuota</th>
                        <th width="30%">Estado</th>
                        <th width="25%" class="text-right">Pendiente</th>
                        <th width="30%" class="text-right">Pagado Hoy</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($group['items'] as $item)
                        @php
                            $paidToday = $item->payments
                                ->where('payment_date', '>=', $date->startOfDay())
                                ->sum('amount');
                            $pending =
                                $item->status !== 'paid' && $item->due_date <= $date
                                    ? $item->amount - $item->amount_paid
                                    : 0;
                        @endphp
                        <tr>
                            <td>#{{ $item->installment_number }}</td>
                            <td>
                                @if ($paidToday > 0 && $pending == 0)
                                    <span class="status-paid">COBRADO</span>
                                @elseif($item->due_date < $date)
                                    <span class="status-pending">VENCIDA</span>
                                @else
                                    <span style="color: #2563eb; font-weight:bold;">VENCE HOY</span>
                                @endif
                            </td>
                            <td class="text-right status-pending">
                                {{ $pending > 0 ? '$ ' . number_format($pending, 2) : '-' }}
                            </td>
                            <td class="text-right status-paid">
                                {{ $paidToday > 0 ? '$ ' . number_format($paidToday, 2) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 40px; color: #666; border: 1px dashed #ccc;">
            <p>No hay visitas pendientes ni cobros realizados para este cobrador en el día de hoy.</p>
        </div>
    @endforelse

</body>

</html>
