<?php

namespace App\Livewire\Treasury;

use App\Models\User;
use App\Src\Treasury\Actions\ProcessSurrenderAction;
use App\Src\Treasury\DTOs\SurrenderData;
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
        $this->amount = $currentBalance; // Sugerimos rendir todo
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['collector', 'amount', 'notes']);
    }

    public function save(ProcessSurrenderAction $action)
    {
        $this->validate();

        $dto = SurrenderData::fromArray([
            'collector_id' => $this->collector->id,
            'amount' => $this->amount,
            'notes' => $this->notes,
        ]);

        $action->execute($dto);

        $this->closeModal();
        $this->dispatch('surrenderProcessed'); // Refresca el tablero principal
        session()->flash('flash.banner', 'Rendición procesada correctamente. El dinero ahora está en Caja Central.');
        session()->flash('flash.bannerStyle', 'success');
    }

    public function render()
    {
        return view('livewire.treasury.surrender-modal')->layout('layouts.app');
    }
}
