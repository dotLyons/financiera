<?php

namespace App\Livewire\Clients;

use App\Src\Client\Actions\CreateClientAction;
use App\Src\Client\DTOs\CreateClientData;
use Livewire\Component;

class Create extends Component
{
    // Propiedades del Formulario
    public $last_name = '';
    public $first_name = '';
    public $dni = '';
    public $phone = '';
    public $address = '';
    public $notes = '';

    protected $rules = [
        'last_name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        // 'unique:table,column' -> validamos que el CUIT no exista ya
        'dni' => 'required|string|max:20|unique:clients,dni',
        'phone' => 'nullable|string|max:50',
        'address' => 'required|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ];

    public function render()
    {
        return view('livewire.clients.create')->layout('layouts.app');
    }

    public function save(CreateClientAction $action)
    {
        // 1. Validar
        $validated = $this->validate();

        // 2. Crear DTO
        $dto = CreateClientData::fromArray($validated);

        // 3. Ejecutar Acción
        $action->execute($dto);

        // 4. Feedback y Redirección
        session()->flash('flash.banner', 'Cliente dado de alta correctamente.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('clients.index');
    }
}
