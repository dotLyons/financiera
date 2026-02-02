<?php

namespace App\Livewire\Collectors;

use App\Models\User;
use App\Src\Collectors\Actions\UpdateCollectorAction;
use App\Src\Collectors\DTOs\UpdateCollectorData;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public User $user; // El cobrador a editar
    public $name;
    public $email;
    public $is_active;

    public function mount(User $user)
    {
        // Bloqueamos que intenten editar un admin u otro rol por URL
        if ($user->role !== 'collector') {
            abort(403, 'Solo se pueden editar cobradores desde este módulo.');
        }

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_active = $user->is_active ? 1 : 0; // Convertimos bool a int para el select
    }

    public function save(UpdateCollectorAction $action)
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            // Validamos que el email sea único, IGNORANDO al usuario actual
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
            'is_active' => 'required|boolean',
        ]);

        $dto = UpdateCollectorData::fromArray($validated);

        $action->execute($this->user, $dto);

        return redirect()->route('collectors.index');
    }

    public function render()
    {
        return view('livewire.collectors.edit')->layout('layouts.app');
    }
}
