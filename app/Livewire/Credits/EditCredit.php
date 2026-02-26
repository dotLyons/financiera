<?php

namespace App\Livewire\Credits;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Models\InstallmentModel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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

    // Variables para la lógica de reestructuración
    public $totalAlreadyPaid = 0;
    public $isRestructuring = false;

    public function mount(CreditsModel $credit)
    {
        $this->credit = $credit;

        // Calculamos cuánto ya se pagó de este crédito
        $this->totalAlreadyPaid = $credit->installments()->sum('amount_paid');
        $this->isRestructuring = $this->totalAlreadyPaid > 0;

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

    // Calculamos el saldo en tiempo real para la vista
    public function getCalculatedTotalProperty()
    {
        $amount_net = (float) $this->amount_net;
        $interest_rate = (float) $this->interest_rate;
        return $amount_net + ($amount_net * ($interest_rate / 100));
    }

    public function update()
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

        $newTotalAmount = $this->calculatedTotal;

        // Validar que el nuevo total no sea menor a lo que ya pagó
        if ($newTotalAmount < $this->totalAlreadyPaid) {
            $this->addError('amount_net', 'El nuevo total ($' . number_format($newTotalAmount, 2) . ') no puede ser menor a lo que el cliente ya pagó ($' . number_format($this->totalAlreadyPaid, 2) . ').');
            return;
        }

        try {
            DB::transaction(function () use ($newTotalAmount) {
                // 1. Guardar historial de pagos antes de borrar las cuotas
                $existingPayments = [];
                if ($this->isRestructuring) {
                    $oldInstallments = $this->credit->installments()->with('payments')->get();
                    foreach ($oldInstallments as $oldInst) {
                        foreach ($oldInst->payments as $payment) {
                            $existingPayments[] = $payment;
                        }
                    }
                }

                // 2. Eliminar cuotas viejas (Los pagos NO se borran por cascade, los reasignaremos)
                $this->credit->installments()->delete();

                // 3. Actualizar datos base del Crédito
                $this->credit->update([
                    'client_id' => $this->client_id,
                    'collector_id' => $this->collector_id,
                    'amount_net' => $this->amount_net,
                    'interest_rate' => $this->interest_rate,
                    'amount_total' => $newTotalAmount,
                    'installments_count' => $this->installments_count,
                    'payment_frequency' => $this->payment_frequency,
                    'start_date' => $this->start_date,
                    'date_of_award' => $this->date_of_award,
                ]);

                // 4. Calcular el monto de cada cuota nueva
                $installmentAmount = $newTotalAmount / $this->installments_count;
                $currentDate = Carbon::parse($this->start_date);
                $moneyToDistribute = $this->totalAlreadyPaid;

                // 5. Generar las nuevas cuotas y reasignar la plata
                for ($i = 1; $i <= $this->installments_count; $i++) {

                    $paidForThisInstallment = 0;
                    $status = 'pending';

                    if ($moneyToDistribute >= $installmentAmount) {
                        $paidForThisInstallment = $installmentAmount;
                        $moneyToDistribute -= $installmentAmount;
                        $status = 'paid';
                    } elseif ($moneyToDistribute > 0) {
                        $paidForThisInstallment = $moneyToDistribute;
                        $moneyToDistribute = 0;
                        $status = 'partial';
                    } else {
                        if ($currentDate->isPast()) {
                            $status = 'overdue';
                        }
                    }

                    $newInst = InstallmentModel::create([
                        'credit_id' => $this->credit->id,
                        'installment_number' => $i,
                        'amount' => $installmentAmount,
                        'amount_paid' => $paidForThisInstallment,
                        'due_date' => $currentDate->format('Y-m-d'),
                        'status' => $status,
                    ]);

                    // Si esta cuota absorbió plata y tenemos recibos huérfanos, se los asignamos a esta cuota
                    if ($paidForThisInstallment > 0 && !empty($existingPayments)) {
                        $payment = array_shift($existingPayments);
                        if ($payment) {
                            $payment->installment_id = $newInst->id;
                            $payment->save();
                        }
                    }

                    // Calcular próxima fecha
                    $freqEnum = PaymentFrequencyEnum::tryFrom($this->payment_frequency);
                    $currentDate = match ($freqEnum) {
                        PaymentFrequencyEnum::DAILY => $currentDate->addDay(),
                        PaymentFrequencyEnum::WEEKLY => $currentDate->addWeek(),
                        PaymentFrequencyEnum::BIWEEKLY => $currentDate->addWeeks(2),
                        PaymentFrequencyEnum::MONTHLY => $currentDate->addMonth(),
                    };
                }
            });

            session()->flash('flash.banner', 'Crédito reestructurado exitosamente. Se conservaron los pagos.');
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
