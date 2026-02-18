<?php

namespace App\Livewire\Credits;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Actions\CreateCreditAction;
use App\Src\Credits\DTOs\CreateCreditData as DTOsCreateCreditData;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateCredit extends Component
{
    // Propiedades del Formulario
    public $client_id = '';
    public $collector_id = '';
    public $amount_net = '';
    public $interest_rate = 20;
    public $installments_count = 10;
    public $payment_frequency = 'daily';
    public $start_date;
    public $date_of_award;

    // --- NUEVAS PROPIEDADES PARA MIGRACIÓN ---
    public $start_installment = 1;
    public $historical_collector_id = '';

    public function mount()
    {
        // Seteamos la fecha de inicio a HOY por defecto
        $this->start_date = now()->format('Y-m-d');
        $this->date_of_award = now()->format('Y-m-d');

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
            'date_of_award' => 'required|date',

            // Validaciones Nuevas (Mantenemos esto por si lo usas en la vista)
            'start_installment' => 'required|integer|min:1|lte:installments_count',
            'historical_collector_id' => [
                'nullable',
                Rule::requiredIf(fn() => $this->start_installment > 1),
                'exists:users,id'
            ],
        ]);

        // 2. Ejecutar la Acción
        $dto = DTOsCreateCreditData::fromArray($validated);

        // IMPORTANTE: Capturamos el crédito creado para obtener su ID
        $credit = $createCreditAction->execute($dto);

        // 3. LÓGICA DE INTERCEPCIÓN (NUEVO)
        // Verificamos si es un crédito histórico (fecha anterior a hoy)
        $startDate = Carbon::parse($this->start_date);
        $today = Carbon::today();

        if ($startDate->lessThan($today)) {
            session()->flash('flash.banner', 'Crédito creado. Al ser una fecha pasada, por favor regularice los pagos históricos.');
            session()->flash('flash.bannerStyle', 'warning'); // Color amarillo/naranja para llamar la atención

            // Redirigimos a la pantalla de regularización
            return redirect()->route('credits.regularize', $credit->id);
        }

        // 4. Feedback normal y Reset (Si es fecha actual o futura)
        session()->flash('flash.banner', 'Crédito creado correctamente.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('credits.index'); // O dashboard, según prefieras
    }
}
