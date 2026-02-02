<?php

namespace App\Livewire\Credits;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Actions\CreateCreditAction;
use App\Src\Credits\DTOs\CreateCreditData as DTOsCreateCreditData;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateCredit extends Component
{
    // Propiedades del Formulario
    public $client_id = '';
    public $collector_id = '';
    public $amount_net = '';
    public $interest_rate = 20; // Default usual
    public $installments_count = 10; // Default usual
    public $payment_frequency = 'daily'; // Default
    public $start_date;

    public function mount()
    {
        // Seteamos la fecha de inicio a HOY por defecto
        $this->start_date = now()->format('Y-m-d');

        // Verificamos autorización al cargar
        $this->authorize('create', \App\Src\Credits\Models\CreditsModel::class);
    }

    public function render()
    {
        return view('livewire.credits.create-credit', [
            // Listados para los Selects
            'clients' => ClientModel::where('status', 'active')->orderBy('last_name')->get(),
            'collectors' => User::where('role', 'collector')->where('is_active', true)->get(),
            'frequencies' => PaymentFrequencyEnum::cases(),
        ])->layout('layouts.app');
    }

    public function save(CreateCreditAction $createCreditAction)
    {
        // 1. Validación
        $validated = $this->validate([
            'client_id' => 'required|exists:clients,id',
            'collector_id' => 'required|exists:users,id',
            'amount_net' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'installments_count' => 'required|integer|min:1',
            'payment_frequency' => ['required', Rule::enum(PaymentFrequencyEnum::class)],
            'start_date' => 'required|date',
        ]);

        // 2. Ejecutar la Acción (Usando el DTO)
        $dto = DTOsCreateCreditData::fromArray($validated);

        $createCreditAction->execute($dto);

        // 3. Feedback y Reset
        session()->flash('flash.banner', 'Crédito creado y cuotas generadas correctamente.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('dashboard'); // O a la lista de créditos
    }
}
