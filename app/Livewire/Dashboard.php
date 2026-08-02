<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Invoice;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $today = now();

        return view('livewire.dashboard', [
            'companyCount' => Company::count(),
            'invoiceCount' => Invoice::count(),
            'total' => Invoice::sum('amount'),
            'monthTotal' => Invoice::where('invoice_year', $today->year)->where('invoice_month', $today->month)->sum('amount'),
            'yearTotal' => Invoice::where('invoice_year', $today->year)->sum('amount'),
            'recent' => Invoice::with('company')->latest('invoice_date')->limit(6)->get(),
        ])->layout('components.layouts.app', ['title' => 'Dashboard']);
    }
}
