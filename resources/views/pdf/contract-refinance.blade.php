<!DOCTYPE html>
<html>

<head>
    <title>Acuerdo de Refinanciación</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .box {
            border: 1px solid #999;
            padding: 10px;
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            width: 40%;
            display: inline-block;
        }

        .alert {
            color: red;
            font-weight: bold;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: center;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>ACUERDO DE REFINANCIACIÓN DE DEUDA</h2>
        <p>Operación Original #{{ $credit->id }} - Refinanciada el {{ $date->format('d/m/Y') }}</p>
    </div>

    <p>
        El cliente <strong>{{ $client->full_name }}</strong> (DNI {{ $client->dni }}) solicita la reestructuración de
        su saldo pendiente.
    </p>

    <h3>1. Estado de Situación (Histórico)</h3>
    <div class="box" style="background: #f0f0f0;">
        <div><span class="label">Total Abonado a la fecha:</span> $ {{ number_format($paidAmount, 2) }}</div>
        <div><span class="label">Cuotas Pagadas:</span> {{ $credit->installments->where('status', 'paid')->count() }}
            cuotas</div>
    </div>

    <h3>2. Nuevo Plan de Pagos (Vigente desde hoy)</h3>
    <div class="box" style="border: 2px solid #333;">
        <div><span class="label">Nuevo Saldo a Financiar:</span> $ {{ number_format($newTotal, 2) }}</div>
        <div><span class="label">Cantidad de Cuotas:</span>
            {{ $credit->installments->where('status', '!=', 'paid')->count() }} (Nuevas)</div>
        <div><span class="label">Frecuencia:</span> {{ strtoupper($credit->payment_frequency->value) }}</div>
        <div><span class="label">Valor Nueva Cuota:</span>
            @php
                $pendingCount = $credit->installments->where('status', '!=', 'paid')->count();
                $quotaValue = $pendingCount > 0 ? $newTotal / $pendingCount : 0;
            @endphp
            $ {{ number_format($quotaValue, 2) }}
        </div>
    </div>

    <h3>3. Condiciones y Penalidades</h3>
    <p>
        Se deja expresa constancia que se mantienen las condiciones de mora originales:
    </p>
    <ul>
        <li>El interés por atraso es del <strong>5% SEMANAL</strong>.</li>
        <li class="alert">Este interés se aplica sobre el TOTAL DEL SALDO PENDIENTE, no sobre la cuota vencida.
        </li>
        <li>Cualquier atraso superior a 7 días activará este recálculo automático.</li>
    </ul>

    <br><br><br>
    <div style="width: 100%; text-align: center;">
        <div style="border-top: 1px solid #000; width: 40%; margin: 0 auto; padding-top: 5px;">
            Firma del Cliente<br>
            Aceptación de nuevas condiciones
        </div>
    </div>

</body>

</html>
