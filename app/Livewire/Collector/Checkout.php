<?php

namespace App\Livewire\Collector;

use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentModel;
use App\Src\Payments\Enums\PaymentMethodsEnum;
use App\Src\Payments\Models\PaymentsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Checkout extends Component
{
    public InstallmentModel $installment;

    public $amount;
    public $payment_method = 'cash'; // Por defecto Efectivo

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'payment_method' => 'required',
    ];

    public function mount(InstallmentModel $installment)
    {
        if ($installment->credit->collector_id !== auth()->id()) {
            abort(403, 'No tienes permiso para cobrar esta cuota.');
        }

        $this->installment = $installment;

        $saldo = $installment->amount - $installment->amount_paid;
        $this->amount = number_format($saldo, 2, '.', '');
    }

    public function processPayment()
    {
        $this->validate();

        $installments = InstallmentModel::where('credit_id', $this->installment->credit_id)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get();

        $moneyInHand = (float) $this->amount;
        $originalReceivedAmount = $moneyInHand;
        $transactionId = 'TX-' . strtoupper(Str::random(8));

        DB::transaction(function () use ($installments, &$moneyInHand, $originalReceivedAmount, $transactionId) {

            foreach ($installments as $inst) {
                if ($moneyInHand <= 0) {
                    break;
                }

                $debt = $inst->amount - $inst->amount_paid;

                $applyAmount = min($moneyInHand, $debt);

                if ($applyAmount > 0) {
                    PaymentsModel::create([
                        'credit_id' => $inst->credit_id,
                        'installment_id' => $inst->id,
                        'amount' => $applyAmount,
                        'received_amount' => $originalReceivedAmount,
                        'transaction_id' => $transactionId,
                        'payment_date' => now(),
                        'user_id' => auth()->id(),
                        'method' => $this->payment_method,
                    ]);

                    $inst->amount_paid += $applyAmount;

                    if (abs($inst->amount - $inst->amount_paid) < 0.1) {
                        $inst->status = 'paid';
                    } else {
                        $inst->status = 'partial';
                    }
                    $inst->save();

                    $moneyInHand -= $applyAmount;
                }
            }
        });

        // Flash message y volver a la hoja de ruta
        session()->flash('flash.banner', '¡Pago registrado exitosamente en cascada!');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('collector.dashboard');
    }

    public function render()
    {
        return view('livewire.collector.checkout', [
            'methods' => PaymentMethodsEnum::cases()
        ])->layout('layouts.app');
    }
}
