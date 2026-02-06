<?php

namespace App\Livewire\Reports;

use App\Services\MonthlyReportService;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $stats = null;

    public function mount()
    {
        // Por defecto, mes actual
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->generateStats();
    }

    public function generateStats()
    {
        $service = new MonthlyReportService();
        $this->stats = $service->getStats($this->selectedMonth, $this->selectedYear);
    }

    public function updated($propertyName)
    {
        $this->generateStats();
    }

    public function render()
    {
        return view('livewire.reports.index')->layout('layouts.app');
    }
}
