<?php

namespace App\Livewire\Credits;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Actions\UpdateCreditAction;
use App\Src\Credits\DTOs\CreateCreditData;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use App\Src\Credits\Models\CreditsModel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditCredit extends Component
{
    public CreditsModel $credit;

    public $client_id;
    public $collector_id;
    public $amount_net;
    public $interest_rate;
    public $installments_count;
    public $payment_frequency;
    public $start_date;
    public $date_of_award;

    public $edition_reason;

    public function mount(CreditsModel $credit)
    {
        if ($credit->installments()->where('status', 'paid')->exists()) {
            return redirect()->route('clients.history', $credit->client_id)
                ->with('flash.banner', 'No se puede editar un crédito con pagos. Utilice Refinanciación.')
                ->with('flash.bannerStyle', 'danger');
        }

        $this->credit = $credit;

        $this->client_id = $credit->client_id;
        $this->collector_id = $credit->collector_id;
        $this->amount_net = $credit->amount_net;
        $this->interest_rate = $credit->interest_rate;
        $this->installments_count = $credit->installments_count;
        $this->payment_frequency = $credit->payment_frequency->value;
        $this->start_date = $credit->start_date->format('Y-m-d');
        $this->date_of_award = $credit->date_of_award
            ? $credit->date_of_award->format('Y-m-d')
            : $credit->created_at->format('Y-m-d');
    }

    public function update(UpdateCreditAction $action)
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'collector_id' => 'required|exists:users,id',
            'amount_net' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'installments_count' => 'required|integer|min:1',
            'payment_frequency' => ['required', Rule::enum(PaymentFrequencyEnum::class)],
            'start_date' => 'required|date',
            'date_of_award' => 'required|date',
            'edition_reason' => 'required|string|min:5|max:255',
        ]);

        try {
            $dto = CreateCreditData::fromArray([
                'client_id' => $this->client_id,
                'collector_id' => $this->collector_id,
                'amount_net' => $this->amount_net,
                'interest_rate' => $this->interest_rate,
                'installments_count' => $this->installments_count,
                'payment_frequency' => $this->payment_frequency,
                'start_date' => $this->start_date,
                'date_of_award' => $this->date_of_award,
                'start_installment' => 1,
                'historical_collector_id' => null,
            ]);

            $action->execute($this->credit, $dto, $this->edition_reason);

            session()->flash('flash.banner', 'Crédito corregido exitosamente.');
            session()->flash('flash.bannerStyle', 'success');

            return redirect()->route('clients.history', $this->credit->client_id);
        } catch (\Exception $e) {
            $this->addError('base', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.credits.edit-credit', [
            'clients' => ClientModel::where('status', 'active')->orderBy('last_name')->get(),
            'collectors' => User::where('role', 'collector')->where('is_active', true)->get(),
            'frequencies' => PaymentFrequencyEnum::cases(),
        ])->layout('layouts.app');
    }
}
