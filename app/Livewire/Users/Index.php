<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
    public string $username = '';
    public string $password = '';
    public bool $active = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(User $user): void
    {
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username ?? '';
        $this->active = $user->active;
        $this->password = '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:100', 'alpha_dash',
                Rule::notIn(['superadmin']),
                Rule::unique('users', 'username')->ignore($this->editingId),
            ],
            'active' => ['boolean'],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:6', 'max:255'],
        ];
        $data = $this->validate($rules);
        $user = $this->editingId ? User::findOrFail($this->editingId) : new User;
        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['username'].'@invoicepro.local',
            'active' => $data['active'],
        ]);
        if ($data['password'] !== '') {
            $user->password = $data['password'];
        }
        $user->save();

        $this->showForm = false;
        session()->flash('message', $this->editingId ? 'User updated.' : 'User created.');
        $this->resetForm();
    }

    public function delete(User $user): void
    {
        if (Auth::id() === $user->id) {
            $this->addError('delete', 'You cannot delete your own account.');

            return;
        }

        $user->delete();
        session()->flash('message', 'User deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'username', 'password']);
        $this->active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => User::query()
                ->when($this->search, fn ($query) => $query
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('username', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
            'currentUserId' => Auth::id(),
        ])->layout('components.layouts.app', ['title' => 'Users']);
    }
}
