<?php

namespace App\Livewire\Collectors;

use App\Models\User;
use App\Src\Credits\Actions\ReassignPortfolioAction;
use App\Src\Credits\Models\CreditsModel;
use Livewire\Component;

class ReassignModal extends Component
{
    public $isOpen = false;
    public ?User $sourceCollector = null;
    public $targetCollectorId = '';

    public $creditsList = [];
    public $selectedCredits = [];
    public $selectAll = false;

    protected $listeners = ['openReassignModal'];

    public function openReassignModal($collectorId)
    {
        $this->reset(['targetCollectorId', 'selectedCredits', 'creditsList', 'selectAll']);

        $this->sourceCollector = User::find($collectorId);

        $rawCredits = CreditsModel::with('client')
            ->where('collector_id', $collectorId)
            ->whereIn('status', ['active', 'refinanced'])
            ->get();

        $this->creditsList = $rawCredits->map(function ($credit) {

            $fullName = 'Desconocido';

            if ($credit->client) {
                $fullName = $credit->client->last_name . ', ' . $credit->client->first_name;
            }

            return [
                'id' => (string) $credit->id,
                'client_name' => $fullName,
                'amount_pending' => $credit->amount_net - $credit->amount_paid,
            ];
        })->toArray();

        $this->isOpen = true;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCredits = array_column($this->creditsList, 'id');
        } else {
            $this->selectedCredits = [];
        }
    }

    public function updatedSelectedCredits()
    {
        $this->selectAll = count($this->selectedCredits) == count($this->creditsList);
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['sourceCollector', 'targetCollectorId', 'selectedCredits', 'creditsList']);
    }

    public function transfer(ReassignPortfolioAction $action)
    {
        $this->validate([
            'targetCollectorId' => 'required|exists:users,id|different:sourceCollector.id',
            'selectedCredits'   => 'required|array|min:1',
        ]);

        $count = $action->execute(
            $this->sourceCollector->id,
            $this->targetCollectorId,
            $this->selectedCredits
        );

        $this->closeModal();
        $this->dispatch('collectorUpdated');
        session()->flash('flash.banner', "Se transfirieron exitosamente $count créditos.");
    }

    public function render()
    {
        $targetCollectors = User::where('role', 'collector')
            ->where('is_active', true)
            ->where('id', '!=', $this->sourceCollector?->id)
            ->get();

        return view('livewire.collectors.reassign-modal', [
            'targetCollectors' => $targetCollectors
        ])->layout('layouts.app');
    }
}
