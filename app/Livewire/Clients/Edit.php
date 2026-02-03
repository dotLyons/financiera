<?php

namespace App\Livewire\Clients;

use App\Src\Client\Actions\UpdateClientAction;
use App\Src\Client\DTOs\UpdateClientData;
use App\Src\Client\Models\ClientModel;
use App\Src\Client\Enum\ClientStatusEnum;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public ClientModel $client;

    // Propiedades del formulario
    public $last_name;
    public $first_name;
    public $dni;
    public $phone;
    public $address;
    public $notes;
    public $status;

    // Cargar datos existentes al iniciar
    public function mount(ClientModel $client)
    {
        $this->client = $client;

        $this->last_name = $client->last_name;
        $this->first_name = $client->first_name;
        $this->dni = $client->dni;
        $this->phone = $client->phone;
        $this->address = $client->address;
        $this->notes = $client->notes;
        $this->status = $client->status->value;
    }

    public function render()
    {
        return view('livewire.clients.edit', [
            'statuses' => ClientStatusEnum::cases(),
        ])->layout('layouts.app');
    }

    public function save(UpdateClientAction $action)
    {
        // 1. Validación Inteligente
        $validated = $this->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            // TRUCO: Validar único en la tabla clients, columna dni, PERO ignorando el ID actual
            'dni' => ['required', 'string', 'max:20', Rule::unique('clients', 'dni')->ignore($this->client->id)],
            'phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => ['required', Rule::enum(ClientStatusEnum::class)],
        ]);

        // 2. DTO
        $dto = UpdateClientData::fromArray($validated);

        // 3. Acción
        $action->execute($this->client, $dto);

        // 4. Feedback
        session()->flash('flash.banner', 'Cliente actualizado correctamente.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('clients.index');
    }
}
