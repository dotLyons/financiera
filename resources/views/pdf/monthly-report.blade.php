<!DOCTYPE html>
<html>

<head>
    <title>Reporte Mensual</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .title {
            text-transform: uppercase;
            font-size: 24px;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .section-title {
            background: #eee;
            padding: 5px 10px;
            font-weight: bold;
            border-left: 5px solid #333;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .kpi-container {
            width: 100%;
            display: table;
            margin-bottom: 20px;
        }

        .kpi-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            display: block;
            margin-top: 5px;
        }

        .kpi-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .positive {
            color: green;
        }

        .negative {
            color: red;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1 class="title">REPORTE MENSUAL DE GESTIÓN</h1>
        <p class="subtitle">Período: {{ strtoupper($stats['period']) }}</p>
    </div>

    <div class="section-title">1. RESUMEN FINANCIERO (CASH FLOW)</div>

    <div class="kpi-container">
        <div class="kpi-box">
            <span class="kpi-label">Total Ingresos (Cobrado)</span>
            <span class="kpi-value positive">$ {{ number_format($stats['financial']['inflow_total'], 2) }}</span>
        </div>
        <div class="kpi-box">
            <span class="kpi-label">Total Egresos (Créditos)</span>
            <span class="kpi-value negative">$ {{ number_format($stats['financial']['outflow_credits'], 2) }}</span>
        </div>
        <div class="kpi-box">
            <span class="kpi-label">Resultado Neto</span>
            <span class="kpi-value">$ {{ number_format($stats['financial']['net_result'], 2) }}</span>
        </div>
    </div>

    <table style="width: 50%; margin-bottom: 20px;">
        <tr>
            <th colspan="2">Desglose de Ingresos</th>
        </tr>
        <tr>
            <td>Efectivo</td>
            <td class="text-right">$ {{ number_format($stats['financial']['inflow_cash'], 2) }}</td>
        </tr>
        <tr>
            <td>Transferencia</td>
            <td class="text-right">$ {{ number_format($stats['financial']['inflow_transfer'], 2) }}</td>
        </tr>
    </table>

    <div class="section-title">2. OPERACIONES DE CRÉDITO</div>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-center">Cantidad / Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nuevos Créditos Otorgados</td>
                <td class="text-center">{{ $stats['operational']['credits_count'] }}</td>
            </tr>
            <tr>
                <td>Refinanciaciones Realizadas</td>
                <td class="text-center">{{ $stats['operational']['refinanced_count'] }}</td>
            </tr>
            <tr>
                <td>Monto Promedio por Crédito</td>
                <td class="text-center">$ {{ number_format($stats['operational']['average_credit'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">3. RENDIMIENTO DE COBRADORES (RANKING)</div>

    <table>
        <thead>
            <tr>
                <th>Cobrador</th>
                <th class="text-center">Créditos Gestionados</th>
                <th class="text-right">Monto Recaudado</th>
                <th class="text-right">% Aporte al Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['collectors'] as $collector)
                <tr>
                    <td>{{ $collector['name'] }}</td>
                    <td class="text-center">{{ $collector['credits_managed'] }}</td>
                    <td class="text-right">$ {{ number_format($collector['collected'], 2) }}</td>
                    <td class="text-right">{{ number_format($collector['contribution_percent'], 1) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hubo actividad de cobradores este mes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #999;">
        Reporte generado automáticamente por el sistema el {{ date('d/m/Y H:i') }}
    </div>

</body>

</html>
