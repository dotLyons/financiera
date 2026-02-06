<!DOCTYPE html>
<html>

<head>
    <title>Reporte Diario</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }

        .summary-box {
            background: #f0f0f0;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LEGAJO DIARIO DE COBRO</h2>
        <p>Cobrador: {{ $user->name }} | Fecha: {{ $date->format('d/m/Y') }}</p>
    </div>

    @if ($metric)
        <div class="summary-box">
            <table style="border: none;">
                <tr>
                    <td style="border:none;"><strong>Meta del Día:</strong> $
                        {{ number_format($metric->expected_amount, 2) }}</td>
                    <td style="border:none;"><strong>Total Recaudado:</strong> $
                        {{ number_format($metric->collected_total, 2) }}</td>
                    <td style="border:none;"><strong>Eficiencia:</strong> {{ $metric->performance_percent }}%</td>
                </tr>
                <tr>
                    <td style="border:none; color: green;">Efectivo: $ {{ number_format($metric->collected_cash, 2) }}
                    </td>
                    <td style="border:none; color: blue;">Transferencia: $
                        {{ number_format($metric->collected_transfer, 2) }}</td>
                    <td style="border:none;"></td>
                </tr>
            </table>
        </div>
    @endif

    <h3>Detalle de Cobranzas</h3>
    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Crédito/Cuota</th>
                <th>Método</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->created_at->format('H:i') }}</td>
                    <td>{{ $payment->installment->credit->client->full_name }}</td>
                    <td>#{{ $payment->installment->credit->id }} - Cuota
                        {{ $payment->installment->installment_number }}</td>
                    <td>{{ $payment->payment_method->value == 'cash' ? 'Efectivo' : 'Transf.' }}</td>
                    <td class="text-right">$ {{ number_format($payment->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL DEL DÍA</th>
                <th class="text-right">$ {{ number_format($payments->sum('amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
