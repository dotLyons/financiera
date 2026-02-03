<!DOCTYPE html>
<html>

<head>
    <title>Detalle de Crédito</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .box {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 20px;
        }

        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.details th {
            background: #eee;
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }

        table.details td {
            border: 1px solid #ddd;
            padding: 5px;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-table td {
            padding: 4px;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2 style="margin:0;">DETALLE DE CRÉDITO #{{ $credit->id }}</h2>
        <p>{{ $credit->client->last_name }}, {{ $credit->client->first_name }} - DNI: {{ $credit->client->dni }}</p>
    </div>

    <div class="box">
        <table class="summary-table">
            <tr>
                <td class="font-bold">Monto Solicitado:</td>
                <td>$ {{ number_format($credit->amount_net, 2) }}</td>
                <td class="font-bold">Fecha Inicio:</td>
                <td>{{ $credit->start_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Total a Devolver:</td>
                <td>$ {{ number_format($credit->amount_total, 2) }}</td>
                <td class="font-bold">Cobrador:</td>
                <td>{{ $credit->collector->name ?? 'Sin asignar' }}</td>
            </tr>
            <tr>
                <td colspan="4">
                    <hr>
                </td>
            </tr>
            <tr>
                <td class="font-bold">Total Pagado:</td>
                <td style="color: green;">$ {{ number_format($totalPaid, 2) }}</td>
                <td class="font-bold">Saldo Pendiente:</td>
                <td style="color: red;">$ {{ number_format($balance, 2) }}</td>
            </tr>
            <tr>
                <td class="font-bold">Progreso Cuotas:</td>
                <td colspan="3">
                    Totales: {{ $stats['total'] }} |
                    Pagadas: {{ $stats['paid'] }} |
                    Pendientes: {{ $stats['pending'] }}
                </td>
            </tr>
        </table>
    </div>

    <h3>Detalle de Cuotas</h3>
    <table class="details">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="15%">Vencimiento</th>
                <th width="15%">Monto Cuota</th>
                <th width="15%">Fecha Pago</th>
                <th width="15%">Monto Pagado</th>
                <th width="15%">Método</th>
                <th width="15%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($credit->installments as $installment)
                <tr>
                    <td class="text-center">{{ $installment->installment_number }}</td>
                    <td>{{ $installment->due_date->format('d/m/Y') }}</td>
                    <td>$ {{ number_format($installment->amount, 2) }}</td>

                    {{-- Lógica para mostrar pagos --}}
                    @php
                        $lastPayment = $installment->payments->last();
                        $paidAmount = $installment->payments->sum('amount');
                    @endphp

                    <td>
                        {{ $lastPayment ? $lastPayment->payment_date->format('d/m/Y') : '-' }}
                    </td>
                    <td>
                        @if ($paidAmount > 0)
                            $ {{ number_format($paidAmount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($lastPayment)
                            {{ $lastPayment->payment_method->value === 'cash' ? 'Efectivo' : 'Transf.' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($installment->status->value === 'paid')
                            <span style="color: green; font-weight:bold;">PAGADO</span>
                        @elseif($installment->status->value === 'partial')
                            <span style="color: orange;">PARCIAL</span>
                        @elseif($installment->due_date < now() && $installment->status->value !== 'paid')
                            <span style="color: red;">MORA</span>
                        @else
                            PENDIENTE
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
