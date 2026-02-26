<?php

namespace App\Livewire\Installments;

use App\Models\User;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditDate extends Component
{
    public $isOpen = false;
    public ?InstallmentModel $installment = null;

    public $newDate;
    public $newAmount;

    public $paymentId = null;
    public $paymentAmount = null;
    public $paymentMethod = null;

    protected $listeners = ['openEditDateModal'];

    public function openEditDateModal($installmentId)
    {
        $this->installment = InstallmentModel::with('payments')->findOrFail($installmentId);

        $this->newDate = Carbon::parse($this->installment->due_date)->format('Y-m-d');
        $this->newAmount = $this->installment->amount;

        $lastPayment = $this->installment->payments->last();

        if ($lastPayment) {
            $this->paymentId = $lastPayment->id;
            $this->paymentAmount = $lastPayment->amount;
            $this->paymentMethod = is_object($lastPayment->payment_method) ? $lastPayment->payment_method->value : $lastPayment->payment_method;
        } else {
            $this->paymentId = null;
            $this->paymentAmount = null;
            $this->paymentMethod = null;
        }

        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'newDate' => 'required|date',
            'newAmount' => 'required|numeric|min:0',
        ];

        if ($this->paymentId) {
            $rules['paymentAmount'] = 'required|numeric|min:0';
            $rules['paymentMethod'] = 'required|in:cash,transfer,debit_card,credit_card,mercadopago';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $this->installment->due_date = $this->newDate;
            $this->installment->amount = $this->newAmount;

            if ($this->paymentId) {
                $payment = PaymentsModel::find($this->paymentId);

                if ($payment) {
                    $oldMethod = is_object($payment->payment_method) ? $payment->payment_method->value : $payment->payment_method;
                    $oldAmount = $payment->amount;

                    $newMethod = $this->paymentMethod;
                    $newAmount = $this->paymentAmount;

                    $collector = User::find($payment->user_id);

                    if ($collector) {
                        if ($oldMethod === 'cash') {
                            $collector->decrement('wallet_balance', $oldAmount);
                        }

                        if ($newMethod === 'cash') {
                            $collector->increment('wallet_balance', $newAmount);
                        }
                    }

                    $payment->payment_method = $newMethod;
                    $payment->amount = $newAmount;
                    $payment->received_amount = $newAmount;
                    $payment->save();

                    $this->installment->amount_paid = $this->installment->payments()->sum('amount');
                }
            }

            if ($this->installment->amount_paid >= ($this->installment->amount - 0.1)) {
                $this->installment->status = 'paid';
            } elseif ($this->installment->amount_paid > 0) {
                $this->installment->status = 'partial';
            } else {
                $this->installment->status = Carbon::parse($this->newDate)->isPast() ? 'overdue' : 'active';
            }

            $this->installment->save();
        });

        $this->isOpen = false;
        $this->dispatch('date-updated');
        $this->redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.installments.edit-date');
    }
}
