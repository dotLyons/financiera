<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MonthlyReportService;
use App\Src\Client\Models\ClientModel;
use App\Src\Collectors\Models\CollectorDailyMetric;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Models\InstallmentModel;
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
        $parsedDate = \Carbon\Carbon::parse($date);

        $payments = \App\Src\Payments\Models\PaymentsModel::where('user_id', $user->id)
            ->whereDate('payment_date', $parsedDate)
            ->with('installment.credit.client')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalCollected = $payments->sum('amount');

        $collectedCash = $payments->filter(function($p) {
            $method = is_object($p->payment_method) ? $p->payment_method->value : $p->payment_method;
            return $method === 'cash';
        })->sum('amount');

        $collectedTransfer = $totalCollected - $collectedCash;

        $expectedAmount = \App\Src\Installments\Models\InstallmentModel::whereHas('credit', function ($q) use ($user) {
            $q->where('collector_id', $user->id);
        })->whereDate('due_date', $parsedDate)->sum('amount');

        if ($expectedAmount > 0) {
            $performancePercent = round(($totalCollected / $expectedAmount) * 100, 1);
        } else {
            $performancePercent = $totalCollected > 0 ? 100 : 0;
        }

        $metric = (object) [
            'expected_amount'     => $expectedAmount,
            'collected_total'     => $totalCollected,
            'performance_percent' => $performancePercent,
            'collected_cash'      => $collectedCash,
            'collected_transfer'  => $collectedTransfer,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.daily-collector-report', [
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

    /**
     * Reporte 8: Hoja de Ruta Actual (Recorrido del Día)
     */
    public function printRoadmap(User $user)
    {
        $today = \Carbon\Carbon::today();

        $installments = InstallmentModel::with(['credit.client', 'payments'])
            ->whereHas('credit', function ($q) use ($user) {
                $q->where('collector_id', $user->id)
                  ->where('status', 'active');
            })
            ->where(function ($q) use ($today) {
                $q->where(function ($sub) use ($today) {
                    $sub->whereDate('due_date', '<=', $today)
                        ->where('status', '!=', 'paid');
                })
                ->orWhereHas('payments', function ($sub) use ($today) {
                    $sub->whereDate('payment_date', $today);
                });
            })
            ->get();

        $roadmap = [];
        $totalPending = 0;
        $totalCollected = 0;

        foreach ($installments->groupBy('credit.client_id') as $clientId => $items) {
            $client = $items->first()->credit->client;

            $clientTotalPending = 0;
            $clientTotalPaidToday = 0;

            foreach ($items as $item) {
                $paidToday = $item->payments->filter(function($p) use ($today) {
                    return \Carbon\Carbon::parse($p->payment_date)->isSameDay($today);
                })->sum('amount');

                $pending = 0;
                if ($item->status !== 'paid' && \Carbon\Carbon::parse($item->due_date)->startOfDay()->lte($today)) {
                    $pending = $item->amount - $item->amount_paid;
                }

                $clientTotalPaidToday += $paidToday;
                $clientTotalPending += $pending;
            }

            $totalPending += $clientTotalPending;
            $totalCollected += $clientTotalPaidToday;

            $roadmap[] = [
                'client' => $client,
                'items' => $items,
                'paid_today' => $clientTotalPaidToday,
                'pending' => $clientTotalPending
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.collector-roadmap', [
            'user' => $user,
            'date' => $today,
            'roadmap' => $roadmap,
            'totalPending' => $totalPending,
            'totalCollected' => $totalCollected
        ]);

        return $pdf->download("Hoja_Ruta_{$user->name}_{$today->format('Y-m-d')}.pdf");
    }
}
