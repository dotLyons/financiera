<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MonthlyReportService;
use App\Src\Client\Models\ClientModel;
use App\Src\Collectors\Models\CollectorDailyMetric;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Payments\Models\PaymentsModel;
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

    /**
     * Reporte 3: Contrato de Alta de Crédito (Nuevo)
     */
    public function printContract(CreditsModel $credit)
    {
        $credit->load('client');

        $pdf = Pdf::loadView('pdf.contract-new', [
            'credit' => $credit,
            'client' => $credit->client,
            'date' => Carbon::now()
        ]);

        return $pdf->download("Contrato_Credito_{$credit->id}.pdf");
    }

    /**
     * Reporte 4: Comprobante de Refinanciación
     */
    public function printRefinance(CreditsModel $credit)
    {
        $credit->load('client', 'installments');

        // Cálculos para el contexto histórico
        $totalPaidHistorically = $credit->installments->where('status', 'paid')->sum('amount_paid');
        $currentTotalDebt = $credit->amount_total; // Este ya es el valor nuevo post-refinanciación

        // El capital refinanciado es aproximadamente el total actual menos los intereses nuevos
        // (Es un dato estimado para mostrar, ya que el registro exacto se transformó)

        $pdf = Pdf::loadView('pdf.contract-refinance', [
            'credit' => $credit,
            'client' => $credit->client,
            'paidAmount' => $totalPaidHistorically,
            'newTotal' => $currentTotalDebt,
            'date' => Carbon::now()
        ]);

        return $pdf->download("Refinanciacion_Credito_{$credit->id}.pdf");
    }

    /**
     * Reporte 5: Comprobante de Pago Individual (Ticket)
     */
    public function printPaymentReceipt(PaymentsModel $payment)
    {
        // Cargamos toda la info necesaria para el recibo
        $payment->load(['user', 'installment.credit.client']);

        $pdf = Pdf::loadView('pdf.payment-receipt', [
            'payment' => $payment,
            'installment' => $payment->installment,
            'credit' => $payment->installment->credit,
            'client' => $payment->installment->credit->client,
        ]);

        // Configuramos el tamaño del papel para que parezca un Ticket (Opcional, o usar A4)
        // Para celulares, A4 suele verse bien, pero si quieres formato ticket usa:
        // $pdf->setPaper([0, 0, 226, 600], 'portrait'); // Ancho aprox 80mm

        // Usaremos A4 estándar para asegurar compatibilidad
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Recibo_Pago_{$payment->id}.pdf");
    }

    /**
     * Reporte 6: Reporte Diario de Cobrador (Legajo)
     */
    public function printDailyReport(User $user, string $date)
    {
        $parsedDate = Carbon::parse($date);

        // 1. Buscamos la métrica guardada (los totales fijos)
        $metric = CollectorDailyMetric::where('user_id', $user->id)
            ->whereDate('date', $parsedDate)
            ->first();

        // 2. Buscamos los movimientos detallados de ese día
        $payments = PaymentsModel::where('user_id', $user->id)
            ->whereDate('payment_date', $parsedDate)
            ->with('installment.credit.client')
            ->orderBy('created_at', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.daily-collector-report', [
            'user' => $user,
            'date' => $parsedDate,
            'metric' => $metric,
            'payments' => $payments
        ]);

        return $pdf->download("Legajo_{$user->name}_{$date}.pdf");
    }

    /**
     * Reporte 7: Reporte Mensual de la Empresa
     */
    public function printMonthlyReport(int $month, int $year)
    {
        $service = new MonthlyReportService();
        $stats = $service->getStats($month, $year);

        $pdf = Pdf::loadView('pdf.monthly-report', [
            'stats' => $stats
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Reporte_Mensual_{$month}_{$year}.pdf");
    }
}
