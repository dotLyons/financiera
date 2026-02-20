<?php

namespace App\Livewire\Payments;

use App\Models\User;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentModel;
use App\Src\Payments\Enums\PaymentMethodsEnum;
use App\Src\Payments\Models\PaymentsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class CreatePayment extends Component
{
    public $isOpen = false;
    public InstallmentModel $installment;

    public $amount;
    public $method = 'cash';

    public $isMixed = false;
    public $secondAmount;
    public $secondMethod = 'transfer';

    protected $listeners = ['openPaymentModal'];

    public function openPaymentModal($installmentId)
    {
        $this->installment = InstallmentModel::with('credit.client')->findOrFail($installmentId);

        $remaining = $this->installment->amount - $this->installment->amount_paid;
        $this->amount = $remaining;

        $this->reset(['isMixed', 'secondAmount', 'secondMethod']);
        $this->isOpen = true;
    }

    public function getCalculatedBalanceProperty()
    {
        $originalDebt = $this->installment->amount - $this->installment->amount_paid;
        $proposedPayment = (float) $this->amount;

        if ($this->isMixed) {
            $proposedPayment += (float) $this->secondAmount;
        }

        return $originalDebt - $proposedPayment;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,transfer,debit_card,credit_card,mercadopago',
        ];

        if ($this->isMixed) {
            $rules['secondAmount'] = 'required|numeric|min:1';
            $rules['secondMethod'] = 'required|in:cash,transfer,debit_card,credit_card,mercadopago|different:method';
        }

        $this->validate($rules);

        $installments = InstallmentModel::where('credit_id', $this->installment->credit_id)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get();

        $totalCreditDebt = $installments->sum(function ($inst) {
            return $inst->amount - $inst->amount_paid;
        });

        $totalProposedPayment = (float) $this->amount;
        if ($this->isMixed) {
            $totalProposedPayment += (float) $this->secondAmount;
        }

        if ($totalProposedPayment > ($totalCreditDebt + 0.01)) {
            $this->addError('amount', 'El pago total ($' . number_format($totalProposedPayment, 2) . ') supera la deuda total del crédito ($' . number_format($totalCreditDebt, 2) . ').');
            return;
        }

        $transactionId = 'TX-' . strtoupper(Str::random(8));
        $lastPaymentId = null;

        DB::transaction(function () use ($installments, $transactionId, &$lastPaymentId) {

            $moneyInHand1 = (float) $this->amount;
            $originalAmount1 = $moneyInHand1;

            foreach ($installments as $inst) {
                if ($moneyInHand1 <= 0) break;

                $debt = $inst->amount - $inst->amount_paid;
                if ($debt <= 0) continue;

                $apply = min($moneyInHand1, $debt);

                $payment = PaymentsModel::create([
                    'credit_id' => $inst->credit_id,
                    'installment_id' => $inst->id,
                    'amount' => $apply,
                    'received_amount' => $originalAmount1,
                    'transaction_id' => $transactionId,
                    'payment_date' => now(),
                    'user_id' => auth()->id(),
                    'method' => $this->method,
                    'notes' => 'Cobro Sistema Admin'
                ]);
                $lastPaymentId = $payment->id;

                $inst->amount_paid += $apply;
                $inst->status = abs($inst->amount - $inst->amount_paid) < 0.1 ? 'paid' : 'partial';
                $inst->save();

                $moneyInHand1 -= $apply;
            }

            if ($this->isMixed && $this->secondAmount > 0) {
                $moneyInHand2 = (float) $this->secondAmount;
                $originalAmount2 = $moneyInHand2;

                foreach ($installments as $inst) {
                    if ($moneyInHand2 <= 0) break;

                    $debt = $inst->amount - $inst->amount_paid;
                    if ($debt <= 0) continue;

                    $apply = min($moneyInHand2, $debt);

                    $payment = PaymentsModel::create([
                        'credit_id' => $inst->credit_id,
                        'installment_id' => $inst->id,
                        'amount' => $apply,
                        'received_amount' => $originalAmount2,
                        'transaction_id' => $transactionId,
                        'payment_date' => now(),
                        'user_id' => auth()->id(),
                        'method' => $this->secondMethod,
                        'notes' => 'Cobro Sistema Admin (Mixto)'
                    ]);
                    $lastPaymentId = $payment->id;

                    $inst->amount_paid += $apply;
                    $inst->status = abs($inst->amount - $inst->amount_paid) < 0.1 ? 'paid' : 'partial';
                    $inst->save();

                    $moneyInHand2 -= $apply;
                }
            }
        });

        $this->isOpen = false;
        $this->dispatch('payment-processed');

        $user = auth()->user();
        if ($user->role === 'collector') {
            $pdfRoute = route('collector.receipt.print', $lastPaymentId);
        } else {
            $pdfRoute = route('receipt.print', $lastPaymentId);
        }

        $this->dispatch('open-pdf', url: $pdfRoute);
    }

    public function render()
    {
        return view('livewire.payments.create-payment', [
            'paymentMethods' => [
                'cash' => 'Efectivo',
                'transfer' => 'Transferencia',
            ]
        ])->layout('layouts.app');
    }
}
