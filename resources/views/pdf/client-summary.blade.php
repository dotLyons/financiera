<!DOCTYPE html>
<html>

<head>
    <title>Resumen Cliente</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .client-info {
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #eee;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }

        .credit-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .credit-header {
            font-weight: bold;
            background: #eee;
            padding: 5px;
            border-bottom: 1px solid #999;
            margin: -10px -10px 10px -10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #000;
        }

        .status-active {
            color: green;
        }

        .status-finished {
            color: blue;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1 style="margin:0;">FINANCIERA</h1>
        <p>Resumen Global de Cliente</p>
        <p style="font-size: 10px;">Generado el: {{ $date->format('d/m/Y H:i') }}</p>
    </div>

    <div class="client-info">
        <table>
            <tr>
                <td width="50%">
                    <span class="label">Cliente:</span> <span class="value">{{ $client->last_name }},
                        {{ $client->first_name }}</span><br>
                    <span class="label">DNI:</span> <span class="value">{{ $client->dni }}</span><br>
                    <span class="label">Rubro</span> <span class="value">{{ $client->rubro }}</span><br>
                    <span class="label">Actividad</span> <span class="value">{{ $client->status ? 'Activo' : 'Inactivo' }}</span>
                </td>
                <td width="50%">
                    <span class="label">Dirección:</span> <span class="value">{{ $client->address }}</span><br>
                    <span class="label">Direccion Alternativa</span> <span class="value">{{ $client->second_address ?? '-' }}</span><br>
                    <span class="label">Teléfono:</span> <span class="value">{{ $client->phone ?? '-' }}</span><br>
                    <span class="label">Telefono de Referencia:</span> <span class="value">{{ $client->reference_phone ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">RESUMEN DE CRÉDITOS</div>

    @forelse($client->credits as $credit)
        <div class="credit-box">
            <div class="credit-header">
                Crédito #{{ $credit->id }} - Solicitado el {{ $credit->start_date->format('d/m/Y') }}
                <span style="float:right;">Estado: {{ strtoupper($credit->status->value ?? $credit->status) }}</span>
            </div>

            <table>
                <tr>
                    <td>
                        <span class="label">Monto Total Crédito:</span><br>
                        $ {{ number_format($credit->amount_total, 2) }}
                    </td>
                    <td>
                        <span class="label">Total Pagado:</span><br>
                        $ {{ number_format($credit->calculated_paid, 2) }}
                    </td>
                    <td>
                        <span class="label">Saldo Adeudado:</span><br>
                        $ {{ number_format($credit->calculated_debt, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 1px dashed #ccc; padding-top: 8px;">
                        <span class="label">Resumen de Cuotas:</span><br>
                        Totales: <b>{{ $credit->count_total }}</b> |
                        Pagadas: <b>{{ $credit->count_paid }}</b> |
                        En Mora: <b style="color:red;">{{ $credit->count_mora }}</b>
                    </td>
                </tr>
            </table>
        </div>
    @empty
        <p style="text-align: center; color: #999;">Este cliente no tiene historial de créditos.</p>
    @endforelse

</body>

</html>
