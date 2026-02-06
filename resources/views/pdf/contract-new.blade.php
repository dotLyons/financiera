<!DOCTYPE html>
<html>

<head>
    <title>Contrato de Crédito</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            color: #555;
        }

        .box {
            border: 1px solid #ccc;
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .label {
            font-weight: bold;
            display: table-cell;
            width: 40%;
        }

        .value {
            display: table-cell;
            width: 60%;
        }

        .warning-box {
            border: 2px solid #d9534f;
            background: #fdf2f2;
            padding: 15px;
            text-align: justify;
            margin-top: 20px;
        }

        .warning-title {
            color: #d9534f;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .signatures {
            margin-top: 60px;
            width: 100%;
        }

        .sig-line {
            border-top: 1px solid #000;
            width: 40%;
            display: inline-block;
            margin-right: 5%;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1 class="title">SOLICITUD DE CRÉDITO Y PAGARÉ</h1>
        <p class="subtitle">Operación #{{ $credit->id }} | Fecha: {{ $credit->start_date->format('d/m/Y') }}</p>
    </div>

    <p>
        Por la presente, <strong>{{ $client->full_name }}</strong>, identificado con DNI
        <strong>{{ $client->dni }}</strong>,
        domiciliado en {{ $client->address }}, declara haber recibido la suma detallada a continuación en concepto de
        préstamo personal.
    </p>

    <div class="box">
        <div class="row"><span class="label">Monto Solicitado (Neto):</span> <span class="value">$
                {{ number_format($credit->amount_net, 2) }}</span></div>
        <div class="row"><span class="label">Total a Devolver:</span> <span class="value">$
                {{ number_format($credit->amount_total, 2) }}</span></div>
        <div class="row"><span class="label">Plan de Pagos:</span> <span
                class="value">{{ $credit->installments_count }} Cuotas</span></div>
        <div class="row"><span class="label">Frecuencia:</span> <span
                class="value">{{ strtoupper($credit->payment_frequency->value) }}</span></div>
        <div class="row"><span class="label">Valor por Cuota:</span> <span class="value">$
                {{ number_format($credit->amount_total / $credit->installments_count, 2) }}</span></div>
        <div class="row"><span class="label">Primer Vencimiento:</span> <span
                class="value">{{ $credit->installments->first()->due_date->format('d/m/Y') }}</span></div>
    </div>

    <div class="warning-box">
        <div class="warning-title">CLÁUSULA DE MORA E INTERESES (IMPORTANTE)</div>
        <p>
            El deudor declara conocer y aceptar que la falta de pago en tiempo y forma generará intereses punitorios
            bajo las siguientes condiciones:
        </p>
        <p>
            <strong>1. CÁLCULO SOBRE SALDO TOTAL:</strong> En caso de atraso superior a 7 días (una semana) en
            cualquiera de las cuotas, se aplicará una penalidad del <strong>5% (cinco por ciento)</strong>.
            Esta penalidad se calcula sobre el <u>SALDO TOTAL PENDIENTE DE PAGO DEL CRÉDITO</u>, y no sobre la
            cuota vencida.
        </p>
        <p>
            <strong>2. ACTUALIZACIÓN DE CUOTAS:</strong> Dicho recargo será prorrateado y sumado a todas las cuotas
            restantes no abonadas, incrementando su valor original.
        </p>
    </div>

    <p style="text-align: justify; margin-top: 20px;">
        El deudor se compromete a cancelar la obligación en los plazos estipulados. La falta de pago faculta al acreedor
        a iniciar las acciones legales correspondientes.
    </p>

    <div class="signatures">
        <div class="sig-line">
            Firma del Cliente<br>
            {{ $client->full_name }}<br>
            DNI: {{ $client->dni }}
        </div>
        <div class="sig-line">
            Firma Responsable / Autorizante<br>
            Financiera
        </div>
    </div>

</body>

</html>
