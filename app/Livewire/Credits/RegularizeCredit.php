<?php

namespace App\Livewire\Credits;

use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentModel;
use App\Src\Payments\Models\PaymentsModel;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RegularizeCredit extends Component
{
    public CreditsModel $credit;
    public $installments = [];

    public $selectedFull = [];
    public $partialAmounts = [];

    public function mount(CreditsModel $credit)
    {
        $this->credit = $credit;

        $this->installments = $credit->installments()
            ->where('due_date', '<=', now())
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get();

        foreach ($this->installments as $inst) {
            $this->partialAmounts[$inst->id] = null;
        }
    }

    public function process()
    {
        DB::transaction(function () {
            foreach ($this->installments as $installment) {

                $amountToPay = 0;
                $isFullPayment = in_array($installment->id, $this->selectedFull);
                $partialInput = (float) ($this->partialAmounts[$installment->id] ?? 0);
                $currentDebt = $installment->amount - $installment->amount_paid;

                if ($isFullPayment) {
                    $amountToPay = $currentDebt;
                } elseif ($partialInput > 0) {
                    $amountToPay = min($partialInput, $currentDebt);
                }

                if ($amountToPay > 0) {

                    PaymentsModel::create([
                        'credit_id' => $this->credit->id,
                        'installment_id' => $installment->id,
                        'amount' => $amountToPay,
                        'payment_date' => $installment->due_date,
                        'user_id' => auth()->id(),
                        'method' => 'migracion',
                        'notes' => 'Regularización de saldo histórico (Migración)'
                    ]);

                    $installment->amount_paid += $amountToPay;

                    if (abs($installment->amount - $installment->amount_paid) < 0.1) {
                        $installment->status = 'paid';
                    } else {
                        $installment->status = 'partial';
                    }

                    $installment->save();
                }
            }
        });

        session()->flash('flash.banner', 'Historial de pagos regularizado con éxito.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('credits.index');
    }

    public function render()
    {
        return view('livewire.credits.regularize-credit')->layout('layouts.app');
    }
}
