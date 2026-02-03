<?php

namespace App\Http\Controllers;

use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Models\CreditsModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Reporte 1: Resumen Global del Cliente
     */
    public function clientSummary(ClientModel $client)
    {
        // Cargamos los créditos y sus cuotas para hacer los cálculos
        $client->load(['credits.installments']);

        // Preparamos los datos para la vista
        // Calculamos métricas "al vuelo" para no cargar la vista con lógica
        foreach ($client->credits as $credit) {
            $credit->calculated_paid = $credit->installments->where('status', 'paid')->sum('amount_paid');
            $credit->calculated_debt = $credit->amount_total - $credit->calculated_paid;

            $credit->count_total = $credit->installments->count();
            $credit->count_paid = $credit->installments->where('status', 'paid')->count();

            // Mora: No pagada y fecha vencida
            $credit->count_mora = $credit->installments
                ->where('status', '!=', 'paid')
                ->where('due_date', '<', Carbon::today())
                ->count();
        }

        $pdf = Pdf::loadView('pdf.client-summary', [
            'client' => $client,
            'date' => Carbon::now()
        ]);

        return $pdf->download("Resumen_Cliente_{$client->dni}.pdf");
    }

    /**
     * Reporte 2: Detalle Específico de un Crédito
     */
    public function creditDetail(CreditsModel $credit)
    {
        // Cargamos cliente, cuotas y los PAGOS de esas cuotas
        $credit->load(['client', 'installments.payments']);

        // Cálculos generales
        $totalPaid = $credit->installments->sum('amount_paid');
        $balance = $credit->amount_total - $totalPaid;

        $countTotal = $credit->installments->count();
        $countPaid = $credit->installments->where('status', 'paid')->count();
        $countPending = $countTotal - $countPaid;

        $pdf = Pdf::loadView('pdf.credit-detail', [
            'credit' => $credit,
            'totalPaid' => $totalPaid,
            'balance' => $balance,
            'stats' => [
                'total' => $countTotal,
                'paid' => $countPaid,
                'pending' => $countPending
            ],
            'date' => Carbon::now()
        ]);

        return $pdf->download("Credito_{$credit->id}_{$credit->client->last_name}.pdf");
    }
}
