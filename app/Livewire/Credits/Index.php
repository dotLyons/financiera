<?php

namespace App\Livewire\Credits;

use App\Models\User;
use App\Src\Credits\Enums\CreditStatusEnum;
use App\Src\Credits\Models\CreditsModel;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';          // Busca por Nombre de Cliente o ID de Crédito
    public $statusFilter = '';    // Filtra por Estado (Active, Paid, Defaulted)
    public $collectorFilter = ''; // Filtra por Cobrador

    // Resetear paginación al filtrar
    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedCollectorFilter() { $this->resetPage(); }

    public function render()
    {
        $credits = CreditsModel::query()
            ->with(['client', 'collector', 'installments']) // Eager Loading vital

            // 1. Filtro de Búsqueda (Por Cliente o ID)
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
                })->orWhere('id', 'like', '%' . $this->search . '%');
            })

            // 2. Filtro de Estado
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })

            // 3. Filtro de Cobrador
            ->when($this->collectorFilter, function ($query) {
                $query->where('collector_id', $this->collectorFilter);
            })

            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.credits.index', [
            'credits' => $credits,
            'statuses' => CreditStatusEnum::cases(),
            'collectors' => User::where('role', 'collector')->where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
