<?php

namespace App\Livewire\Payments;

use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentModel;
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

        // Deuda de cuota pura + Deuda de mora
        $regularDebt = max(0, $this->installment->amount - $this->installment->amount_paid);
        $punitoryDebt = max(0, $this->installment->punitory_interest - $this->installment->punitory_paid);

        $totalDebt = $regularDebt + $punitoryDebt;
        $this->amount = $totalDebt > 0 ? $totalDebt : 0;

        $this->reset(['isMixed', 'secondAmount', 'secondMethod']);
        $this->isOpen = true;
    }

    public function getCalculatedBalanceProperty()
    {
        $regularDebt = $this->installment->amount - $this->installment->amount_paid;
        $punitoryDebt = $this->installment->punitory_interest - $this->installment->punitory_paid;

        $originalDebt = $regularDebt + $punitoryDebt;
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

    private function distributePayment(float $paymentAmount)
    {
        $regularDebt = $this->installment->amount - $this->installment->amount_paid;

        if ($paymentAmount <= $regularDebt) {
            $this->installment->amount_paid += $paymentAmount;
        } else {
            $this->installment->amount_paid += $regularDebt;
            $leftover = $paymentAmount - $regularDebt;
            $this->installment->punitory_paid += $leftover;
        }
    }

    public function save()
    {
        $rules = [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,transfer,debit_card,credit_card,mercadopago',
        ];

        if ($this->isMixed) {
            $rules['secondAmount'] = 'required|numeric|min:1';
            $rules['secondMethod'] = 'required|different:method';
        }

        $this->validate($rules);

        $transactionId = 'TX-' . strtoupper(Str::random(8));
        $lastPaymentId = null;

        DB::transaction(function () use ($transactionId, &$lastPaymentId) {

            $payment1 = PaymentsModel::create([
                'credit_id' => $this->installment->credit_id,
                'installment_id' => $this->installment->id,
                'amount' => (float) $this->amount,
                'received_amount' => (float) $this->amount,
                'transaction_id' => $transactionId,
                'payment_date' => now(),
                'user_id' => auth()->id(),
                'method' => $this->method,
                'notes' => 'Cobro Sistema Admin'
            ]);
            $lastPaymentId = $payment1->id;

            $this->distributePayment((float) $this->amount);

            if ($this->isMixed && $this->secondAmount > 0) {
                $payment2 = PaymentsModel::create([
                    'credit_id' => $this->installment->credit_id,
                    'installment_id' => $this->installment->id,
                    'amount' => (float) $this->secondAmount,
                    'received_amount' => (float) $this->secondAmount,
                    'transaction_id' => $transactionId,
                    'payment_date' => now(),
                    'user_id' => auth()->id(),
                    'method' => $this->secondMethod,
                    'notes' => 'Cobro Sistema Admin (Mixto)'
                ]);
                $lastPaymentId = $payment2->id;

                $this->distributePayment((float) $this->secondAmount);
            }

            if ($this->installment->amount_paid >= ($this->installment->amount - 0.1)) {
                $this->installment->status = 'paid';
            } else {
                $this->installment->status = 'partial';
            }

            $this->installment->save();
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
