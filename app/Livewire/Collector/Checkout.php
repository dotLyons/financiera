<?php

namespace App\Livewire\Collector;

use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Actions\ProcessPaymentAction;
use App\Src\Payments\DTOs\CreatePaymentData;
use App\Src\Payments\Enums\PaymentMethodsEnum;
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
        // Seguridad: Verificar que esta cuota sea de un cliente de este cobrador
        if ($installment->credit->collector_id !== auth()->id()) {
            abort(403, 'No tienes permiso para cobrar esta cuota.');
        }

        $this->installment = $installment;

        // Sugerimos el saldo pendiente
        $saldo = $installment->amount - $installment->amount_paid;
        $this->amount = number_format($saldo, 2, '.', '');
    }

    public function processPayment(ProcessPaymentAction $action)
    {
        $this->validate();

        $dto = CreatePaymentData::fromArray([
            'installment_id' => $this->installment->id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
        ]);

        $action->execute($dto);

        // Flash message y volver a la hoja de ruta
        session()->flash('flash.banner', '¡Pago registrado exitosamente!');
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
