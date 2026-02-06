<?php

namespace App\Livewire\Credits;

use App\Src\Credits\Actions\RefinanceCreditAction;
use App\Src\Credits\Models\CreditsModel;
use Carbon\Carbon;
use Livewire\Component;

class RefinanceModal extends Component
{
    public $isOpen = false;
    public ?CreditsModel $credit = null;

    public $outstandingBalance = 0; // Saldo actual deudor
    public $newInterest = 10; // % sugerido
    public $newInstallments = 1;
    public $newFrequency = 'weekly';
    public $startDate;

    // Calculados
    public $newTotal = 0;
    public $newInstallmentAmount = 0;

    protected $listeners = ['openRefinanceModal'];

    public function openRefinanceModal($creditId)
    {
        $this->credit = CreditsModel::find($creditId);

        $this->outstandingBalance = $this->credit->installments
            ->sum(fn($i) => $i->amount - $i->amount_paid);

        $this->newFrequency = $this->credit->payment_frequency->value ?? 'weekly';
        $this->startDate = Carbon::tomorrow()->format('Y-m-d');

        $this->calculatePreview();
        $this->isOpen = true;
    }

    public function updated($propertyName)
    {
        $this->calculatePreview();
    }

    public function calculatePreview()
    {
        if ($this->outstandingBalance > 0 && $this->newInstallments > 0) {
            $this->newTotal = $this->outstandingBalance * (1 + ($this->newInterest / 100));
            $this->newInstallmentAmount = $this->newTotal / $this->newInstallments;
        }
    }

    public function refinance(RefinanceCreditAction $action)
    {
        $this->validate([
            'newInstallments' => 'required|numeric|min:1',
            'newInterest' => 'required|numeric|min:0',
            'startDate' => 'required|date',
        ]);

        try {
            $action->execute(
                $this->credit,
                $this->newInstallments,
                $this->newFrequency,
                $this->newInterest,
                Carbon::parse($this->startDate)
            );

            $this->isOpen = false;
            $this->dispatch('refreshCredits');
            session()->flash('flash.banner', 'Crédito refinanciado exitosamente.');

            return redirect()->route('clients.history', $this->credit->client_id);
        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.credits.refinance-modal')->layout('layouts.app');
    }
}
