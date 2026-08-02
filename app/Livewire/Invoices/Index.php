<?php

namespace App\Livewire\Invoices;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $company_id = null;
    public string $invoice_number = '';
    public string $work_order = '';
    public string $service_agreement = '';
    public string $to_reference = '';
    public string $invoice_date = '';
    public string $amount = '';
    public string $exchange_rate = '1.000000';
    public string $location = '';
    public string $notes = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function create(): void { $this->resetForm(); $this->invoice_date = now()->toDateString(); $this->showForm = true; }

    public function edit(Invoice $invoice): void
    {
        $this->editingId = $invoice->id;
        $this->fill([
            'company_id' => $invoice->company_id, 'invoice_number' => $invoice->invoice_number,
            'work_order' => $invoice->work_order ?? '', 'service_agreement' => $invoice->service_agreement ?? '',
            'to_reference' => $invoice->to_reference ?? '', 'invoice_date' => $invoice->invoice_date->toDateString(), 'amount' => (string) $invoice->amount,
            'exchange_rate' => (string) $invoice->exchange_rate, 'location' => $invoice->location,
            'notes' => $invoice->notes ?? '',
        ]);
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'invoice_number' => ['required', 'max:255', Rule::unique('invoices')->ignore($this->editingId)],
            'work_order' => ['nullable', 'string', 'max:255'],
            'service_agreement' => ['nullable', 'string', 'max:255'],
            'to_reference' => ['nullable', 'string', 'max:255'],
            'invoice_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'exchange_rate' => ['required', 'numeric', 'gt:0'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        Invoice::updateOrCreate(['id' => $this->editingId], $data);
        $this->showForm = false;
        session()->flash('message', $this->editingId ? 'Invoice updated.' : 'Invoice added.');
        $this->resetForm();
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
        session()->flash('message', 'Invoice deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'company_id', 'invoice_number', 'work_order', 'service_agreement', 'to_reference', 'invoice_date', 'amount', 'location', 'notes']);
        $this->exchange_rate = '1.000000';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.invoices.index', [
            'invoices' => Invoice::with('company')->search($this->search)->latest('invoice_date')->paginate(10),
            'companies' => Company::orderBy('name')->get(),
        ])->layout('components.layouts.app', ['title' => 'Invoices']);
    }
}
