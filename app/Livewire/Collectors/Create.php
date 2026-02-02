<?php

namespace App\Livewire\Collectors;

use App\Src\Collectors\Actions\CreateCollectorAction;
use App\Src\Collectors\DTOs\CreateCollectorData;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $email = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
    ];

    public function render()
    {
        return view('livewire.collectors.create')->layout('layouts.app');
    }

    public function save(CreateCollectorAction $action)
    {
        $validated = $this->validate();

        $dto = CreateCollectorData::fromArray($validated);

        $action->execute($dto);

        session()->flash('flash.banner', 'Cobrador creado con éxito. Contraseña por defecto: "cobrador".');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('collectors.index');
    }
}
