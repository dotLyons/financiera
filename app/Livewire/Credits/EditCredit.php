<?php

namespace App\Livewire\Credits;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        if ($newTotalAmount < $this->totalAlreadyPaid) {
            $this->addError('amount_net', 'El nuevo total ($' . number_format($newTotalAmount, 2) . ') no puede ser menor a lo que el cliente ya pagó ($' . number_format($this->totalAlreadyPaid, 2) . ').');
            return;
        }

        try {
            DB::transaction(function () use ($newTotalAmount) {

                // 1. Rescatar método de pago buscando por las cuotas, NO por credit_id
                $oldMethod = 'cash';
                $oldUserId = auth()->id();
                $existingPayments = [];

                if ($this->isRestructuring) {
                    $oldInstallments = $this->credit->installments()->with('payments')->get();
                    foreach ($oldInstallments as $oldInst) {
                        foreach ($oldInst->payments as $payment) {
                            $existingPayments[] = $payment;
                        }
                    }

                    if (!empty($existingPayments)) {
                        // Tomamos los datos del último pago realizado para usar de referencia
                        $lastPayment = end($existingPayments);
                        $oldMethod = is_object($lastPayment->payment_method) ? $lastPayment->payment_method->value : $lastPayment->payment_method;
                        $oldUserId = $lastPayment->user_id;
                    }
                }

                // 2. Eliminar cuotas viejas (La BD elimina los pagos vinculados por cascada)
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

                // 4. Calcular el monto base de cada cuota con REDONDEO
                $baseInstallmentAmount = round($newTotalAmount / $this->installments_count, 0);
                $currentDate = Carbon::parse($this->start_date);
                $moneyToDistribute = $this->totalAlreadyPaid;
                $accumulatedAmount = 0;

                // 5. Generar las nuevas cuotas y asignar la plata
                for ($i = 1; $i <= $this->installments_count; $i++) {

                    if ($i == $this->installments_count) {
                        $installmentAmount = $newTotalAmount - $accumulatedAmount;
                    } else {
                        $installmentAmount = $baseInstallmentAmount;
                    }
                    $accumulatedAmount += $installmentAmount;

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

                    // 6. CREAR NUEVO RECIBO FÍSICO
                    // Esto es lo que va a permitir que tu modal pueda editar el método de pago
                    if ($paidForThisInstallment > 0) {
                        PaymentsModel::create([
                            'installment_id' => $newInst->id, // El recibo se ata firmemente a la cuota nueva
                            'amount' => $paidForThisInstallment,
                            'received_amount' => $paidForThisInstallment,
                            'transaction_id' => 'TX-RES-' . strtoupper(Str::random(6)),
                            'payment_date' => now(),
                            'user_id' => $oldUserId,
                            'payment_method' => $oldMethod,
                            'notes' => 'Saldo consolidado por reestructuración'
                        ]);
                    }

                    $freqEnum = PaymentFrequencyEnum::tryFrom($this->payment_frequency);
                    $currentDate = match ($freqEnum) {
                        PaymentFrequencyEnum::DAILY => $currentDate->addDay(),
                        PaymentFrequencyEnum::WEEKLY => $currentDate->addWeek(),
                        PaymentFrequencyEnum::MONTHLY => $currentDate->addMonth(),
                    };
                }
            });

            session()->flash('flash.banner', 'Crédito reestructurado exitosamente. Se consolidaron los pagos.');
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
