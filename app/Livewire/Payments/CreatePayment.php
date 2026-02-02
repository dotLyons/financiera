<?php

namespace App\Livewire\Payments;

use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Actions\ProcessPaymentAction;
use App\Src\Payments\DTOs\CreatePaymentData;
use App\Src\Payments\Enums\PaymentMethodsEnum;
use Livewire\Component;

class CreatePayment extends Component
{
    public $isOpen = false;
    public ?InstallmentModel $installment = null;

    // Formulario
    public $amount;
    public $payment_method = 'cash';

    protected $listeners = ['openPaymentModal'];

    // Validaciones
    protected function rules()
    {
        return [
            'amount' => 'required|numeric|min:1', // ¿Permitimos sobrepagos? Por ahora no validamos max
            'payment_method' => 'required',
        ];
    }

    public function openPaymentModal($installmentId)
    {
        $this->installment = InstallmentModel::find($installmentId);

        // Sugerimos pagar el saldo restante
        $saldo = $this->installment->amount - $this->installment->amount_paid;
        $this->amount = number_format($saldo, 2, '.', ''); 

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['installment', 'amount']);
    }

    public function save(ProcessPaymentAction $action)
    {
        $this->validate();

        $dto = CreatePaymentData::fromArray([
            'installment_id' => $this->installment->id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
        ]);

        $action->execute($dto);

        // Feedback y Cierre
        $this->closeModal();
        $this->dispatch('paymentProcessed');

        session()->flash('flash.banner', 'Pago registrado exitosamente.');
    }

    public function render()
    {
        return view('livewire.payments.create-payment', [
            'methods' => PaymentMethodsEnum::cases()
        ]);
    }
}
