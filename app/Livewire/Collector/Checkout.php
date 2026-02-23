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
    public $payment_method = 'cash';

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'payment_method' => 'required',
    ];

    public function mount(InstallmentModel $installment)
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $installment->credit->collector_id !== $user->id) {
            abort(403, 'No tienes permiso para cobrar esta cuota.');
        }

        $this->installment = $installment;

        $regularDebt = max(0, $installment->amount - $installment->amount_paid);
        $punitoryDebt = max(0, $installment->punitory_interest - $installment->punitory_paid);

        $totalDebt = $regularDebt + $punitoryDebt;
        $this->amount = $totalDebt > 0 ? number_format($totalDebt, 2, '.', '') : '0.00';
    }

    public function processPayment()
    {
        $this->validate();

        $transactionId = 'TX-' . strtoupper(Str::random(8));
        $paymentAmount = (float) $this->amount;

        DB::transaction(function () use ($paymentAmount, $transactionId) {

            PaymentsModel::create([
                'credit_id' => $this->installment->credit_id,
                'installment_id' => $this->installment->id,
                'amount' => $paymentAmount,
                'received_amount' => $paymentAmount,
                'transaction_id' => $transactionId,
                'payment_date' => now(),
                'user_id' => auth()->id(),
                'method' => $this->payment_method,
            ]);

            $regularDebt = $this->installment->amount - $this->installment->amount_paid;

            if ($paymentAmount <= $regularDebt) {
                $this->installment->amount_paid += $paymentAmount;
            } else {
                $this->installment->amount_paid += $regularDebt;
                $leftover = $paymentAmount - $regularDebt;
                $this->installment->punitory_paid += $leftover;
            }

            if ($this->installment->amount_paid >= ($this->installment->amount - 0.1)) {
                $this->installment->status = 'paid';
            } else {
                $this->installment->status = 'partial';
            }

            $this->installment->save();
        });

        session()->flash('flash.banner', '¡Cobro registrado exitosamente!');
        session()->flash('flash.bannerStyle', 'success');

        if (auth()->user()->role === 'admin') {
            return redirect()->route('clients.history', $this->installment->credit->client_id);
        }

        return redirect()->route('collector.dashboard');
    }

    public function render()
    {
        return view('livewire.collector.checkout', [
            'methods' => PaymentMethodsEnum::cases()
        ])->layout('layouts.app');
    }
}
