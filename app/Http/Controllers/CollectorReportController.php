<?php

namespace App\Http\Controllers;

use App\Src\Payments\Models\PaymentsModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CollectorReportController extends Controller
{
    public function printPaymentReceipt(PaymentsModel $payment)
    {
        $pdf = Pdf::loadView('pdf.payment-receipt', [
            'payment' => $payment,
            'is_collector_copy' => true
        ]);

        return $pdf->stream('recibo-cobro-' . $payment->id . '.pdf');
    }
}
