<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $contact_person = '';
    public string $notes = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Company $company): void
    {
        $this->editingId = $company->id;
        $this->fill($company->only(['name', 'address', 'phone', 'email', 'contact_person', 'notes']));
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        Company::updateOrCreate(['id' => $this->editingId], $data);
        $this->showForm = false;
        session()->flash('message', $this->editingId ? 'Company updated.' : 'Company added.');
        $this->resetForm();
    }

    public function delete(Company $company): void
    {
        if ($company->invoices()->exists()) {
            $this->addError('delete', 'Delete or reassign this company’s invoices first.');
            return;
        }
        $company->delete();
        session()->flash('message', 'Company deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'address', 'phone', 'email', 'contact_person', 'notes']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.companies.index', [
            'companies' => Company::withCount('invoices')->when($this->search, fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('contact_person', 'like', "%{$this->search}%"))->latest()->paginate(10),
        ])->layout('components.layouts.app', ['title' => 'Companies']);
    }
}
