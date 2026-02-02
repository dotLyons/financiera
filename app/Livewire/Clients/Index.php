<?php

namespace App\Livewire\Clients;

use App\Src\Client\Models\ClientModel;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Propiedades para búsqueda y filtros
    public $search = '';
    public $filterField = 'last_name'; // Campo por defecto: Apellido

    // Mapeo amigable para el usuario (clave DB => etiqueta Vista)
    public $filterOptions = [
        'last_name' => 'Apellido',
        'first_name' => 'Nombre',
        'dni' => 'CUIT', // Aquí está el detalle que pediste: DB es 'dni', vista es 'CUIT'
    ];

    // Resetear paginación cuando se busca algo nuevo
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Resetear paginación si cambia el tipo de filtro
    public function updatingFilterField()
    {
        $this->resetPage();
        $this->search = ''; // Opcional: limpiar búsqueda al cambiar filtro
    }

    public function render()
    {
        $clients = ClientModel::query()
            ->when($this->search, function ($query) {
                // Filtro dinámico: where(campo_seleccionado, like, valor)
                $query->where($this->filterField, 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc') // Los más nuevos primero
            ->paginate(10);

        return view('livewire.clients.index', [
            'clients' => $clients
        ])->layout('layouts.app');
    }
}
