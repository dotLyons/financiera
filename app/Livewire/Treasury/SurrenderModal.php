<?php

namespace App\Livewire\Treasury;

use App\Models\User;
use App\Src\CashOperation\Actions\SurrenderCashAction;
use Livewire\Component;

class SurrenderModal extends Component
{
    public $isOpen = false;
    public ?User $collector = null;

    public $amount;
    public $notes;
    public $maxAmount = 0; // Para validación visual

    protected $listeners = ['openSurrenderModal'];

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'notes' => 'nullable|string|max:255',
    ];

    public function openSurrenderModal($collectorId, $currentBalance)
    {
        $this->collector = User::find($collectorId);
        $this->maxAmount = $currentBalance;

        $this->amount = $currentBalance > 0 ? $currentBalance : '';

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['collector', 'amount', 'notes', 'maxAmount']);
    }

    public function save(SurrenderCashAction $action)
    {
        $this->validate();

        $action->execute(
            collectorId: $this->collector->id,
            adminId: auth()->id(), // El usuario logueado (Admin) es quien recibe
            amount: $this->amount,
            notes: $this->notes
        );

        $this->closeModal();

        $this->dispatch('surrenderProcessed');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Rendición procesada correctamente. Saldo actualizado.'
        ]);
    }

    public function render()
    {
        return view('livewire.treasury.surrender-modal')->layout('layouts.app');
    }
}
