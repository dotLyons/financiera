<?php

namespace App\Livewire\Installments;

use App\Src\Installments\Models\InstallmentModel;
use Carbon\Carbon;
use Livewire\Component;

class EditDate extends Component
{
    public $isOpen = false;
    public ?InstallmentModel $installment = null;

    public $newDate;

    protected $listeners = ['openEditDateModal'];

    public function openEditDateModal($installmentId)
    {
        $this->installment = InstallmentModel::findOrFail($installmentId);

        $this->newDate = Carbon::parse($this->installment->due_date)->format('Y-m-d');

        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'newDate' => 'required|date',
        ]);

        $this->installment->due_date = $this->newDate;
        $this->installment->save();

        $this->isOpen = false;

        $this->dispatch('date-updated');

        $this->redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.installments.edit-date');
    }
}
