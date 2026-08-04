<?php

namespace App\Livewire\Administrations;

use App\Models\Administration;
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
    public string $notes = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Administration $administration): void
    {
        $this->editingId = $administration->id;
        $this->name = $administration->name;
        $this->notes = $administration->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('administrations')->ignore($this->editingId)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Administration::updateOrCreate(['id' => $this->editingId], $data);
        session()->flash('message', $this->editingId ? __('Administration updated.') : __('Administration added.'));
        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(Administration $administration): void
    {
        if ($administration->invoices()->exists()) {
            $this->addError('delete', __('Reassign this administration’s invoices before deleting it.'));
            return;
        }

        $administration->delete();
        session()->flash('message', __('Administration deleted.'));
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'notes']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.administrations.index', [
            'administrations' => Administration::withCount('invoices')
                ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
        ])->layout('components.layouts.app', ['title' => __('Administrations')]);
    }
}
