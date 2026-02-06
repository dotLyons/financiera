<?php

namespace App\Livewire\Collectors;

use App\Models\User;
use App\Src\Credits\Actions\ReassignPortfolioAction;
use Livewire\Component;

class ReassignModal extends Component
{
    public $isOpen = false;
    public ?User $sourceCollector = null;
    public $targetCollectorId = '';

    public $activeCreditsCount = 0;

    protected $listeners = ['openReassignModal'];

    public function openReassignModal($collectorId)
    {
        $this->sourceCollector = User::find($collectorId);

        $this->activeCreditsCount = \App\Src\Credits\Models\CreditsModel::where('collector_id', $collectorId)
            ->whereIn('status', ['active', 'refinanced'])
            ->count();

        $this->targetCollectorId = '';
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['sourceCollector', 'targetCollectorId']);
    }

    public function transfer(ReassignPortfolioAction $action)
    {
        $this->validate([
            'targetCollectorId' => 'required|exists:users,id|different:sourceCollector.id',
        ]);

        $count = $action->execute($this->sourceCollector->id, $this->targetCollectorId);

        $this->closeModal();

        // Emitir evento para refrescar la tabla de cobradores (si muestras contadores)
        $this->dispatch('collectorUpdated');

        // Notificación de éxito (si usas banner o toast)
        session()->flash('flash.banner', "Se transfirieron exitosamente $count créditos.");
    }

    public function render()
    {
        // Listar todos los cobradores ACTIVOS excepto el actual (origen)
        $targetCollectors = User::where('role', 'collector')
            ->where('is_active', true)
            ->where('id', '!=', $this->sourceCollector?->id)
            ->get();

        return view('livewire.collectors.reassign-modal', [
            'targetCollectors' => $targetCollectors
        ])->layout('layouts.app');
    }
}
