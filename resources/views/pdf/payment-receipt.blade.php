<!DOCTYPE html>
<html>

<head>
    <title>Comprobante de Pago</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            font-size: 14px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .subtitle {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }

        .row {
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 40%;
        }

        .value {
            display: inline-block;
            width: 55%;
            text-align: right;
            font-weight: bold;
        }

        .amount-box {
            background: #f0fdf4;
            border: 2px solid #16a34a;
            color: #166534;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            border-radius: 8px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1 class="title">COMPROBANTE DE PAGO</h1>
            <p class="subtitle">Recibo N°: {{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</p>
            <p class="subtitle">{{ $payment->created_at->format('d/m/Y H:i') }} hs</p>
        </div>

        <div class="row">
            <span class="label">Cliente:</span>
            <span class="value">{{ $client->full_name }}</span>
        </div>
        <div class="row">
            <span class="label">DNI:</span>
            <span class="value">{{ $client->dni }}</span>
        </div>
        <div class="row">
            <span class="label">Cobrador:</span>
            <span class="value">{{ $payment->user->name }}</span>
        </div>

        <div class="amount-box">
            $ {{ number_format($payment->amount, 2) }}
        </div>

        <div class="row">
            <span class="label">Método de Pago:</span>
            <span class="value">
                {{ $payment->payment_method->value === 'cash' ? 'EFECTIVO' : 'TRANSFERENCIA' }}
            </span>
        </div>

        <div style="margin-top: 20px;">
            <p style="font-weight: bold; text-decoration: underline;">Detalle de Imputación:</p>
            <div class="row">
                <span class="label">Crédito N°:</span>
                <span class="value">#{{ $credit->id }}</span>
            </div>
            <div class="row">
                <span class="label">Cuota Abonada:</span>
                <span class="value">Cuota {{ $installment->installment_number }} de
                    {{ $credit->installments_count }}</span>
            </div>

            @php
                // Calculamos saldo restante al vuelo
                $totalPaid = $credit->installments->sum('amount_paid'); // Incluye este pago ya guardado
                $balance = $credit->amount_total - $totalPaid;
            @endphp

            <div class="row">
                <span class="label">Saldo Restante Crédito:</span>
                <span class="value" style="color: #d9534f;">$ {{ number_format($balance, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            Documento generado electrónicamente por Sistema Financiero.<br>
            Este comprobante tiene validez interna y administrativa.
        </div>
    </div>

</body>

</html>
