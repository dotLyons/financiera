<?php

namespace App\Livewire\Payments;

use App\Models\User;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Actions\ProcessPaymentAction;
use App\Src\Payments\DTOs\CreatePaymentData;
use App\Src\Payments\Enums\PaymentMethodEnum;
use App\Src\Payments\Enums\PaymentMethodsEnum;
use Livewire\Attributes\Computed;
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

    public function save(ProcessPaymentAction $action)
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

        $pendingBalance = round($this->installment->amount - $this->installment->amount_paid, 2);

        $totalProposedPayment = (float) $this->amount;

        if ($this->isMixed) {
            $totalProposedPayment += (float) $this->secondAmount;
        }

        if ($totalProposedPayment > ($pendingBalance + 0.01)) {
            $this->addError('amount', 'El pago total ($' . number_format($totalProposedPayment, 2) . ') supera el saldo pendiente de la cuota ($' . number_format($pendingBalance, 2) . ').');
            return;
        }

        $dto1 = new CreatePaymentData(
            installmentId: $this->installment->id,
            userId: auth()->id(),
            amount: $this->amount,
            method: PaymentMethodsEnum::from($this->method),
            paymentDate: now(),
            proofOfPayment: 'Cobro Admin'
        );

        $payment1 = $action->execute($dto1);
        $lastPaymentId = $payment1->id;

        if ($this->isMixed && $this->secondAmount > 0) {
            $dto2 = new CreatePaymentData(
                installmentId: $this->installment->id,
                userId: auth()->id(),
                amount: $this->secondAmount,
                method: PaymentMethodsEnum::from($this->secondMethod),
                paymentDate: now(),
                proofOfPayment: 'Cobro Admin (Mixto)'
            );
            $payment2 = $action->execute($dto2);
            $lastPaymentId = $payment2->id;
        }

        $this->isOpen = false;
        $this->dispatch('payment-processed');

        $this->dispatch('open-pdf', url: route('receipt.print', $lastPaymentId));

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.payments.create-payment', [
            'paymentMethods' => [
                'cash' => 'Efectivo',
                'transfer' => 'Transferencia',
                //'debit_card' => 'Tarjeta Débito',
                //'credit_card' => 'Tarjeta Crédito',
                //'mercadopago' => 'Mercado Pago',
            ]
        ])->layout('layouts.app');
    }
}
