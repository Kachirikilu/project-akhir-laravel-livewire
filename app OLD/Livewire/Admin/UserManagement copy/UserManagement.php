<?php

namespace App\Livewire\Admin\UserManagement;

use Livewire\Component;
use Livewire\WithPagination;

use App\Livewire\Admin\UserManagement\Concerns\WithUserQuery;
use App\Livewire\Admin\UserManagement\Concerns\WithUserFilters;
use App\Livewire\Admin\UserManagement\Concerns\WithProdiAutocomplete;
use App\Livewire\Admin\UserManagement\Concerns\WithUserCrud;
use App\Livewire\Admin\UserManagement\Concerns\WithUserDelete;

use Illuminate\Validation\Rule;

class UserManagement extends Component
{
    use WithPagination;
    use WithUserQuery;
    use WithUserFilters;
    use WithProdiAutocomplete;
    use WithUserCrud;
    use WithUserDelete;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['refresh-table' => '$refresh'];

    public ?int $selectedProdiId = null;
    public string $prodiSearchQuery = '';
    public array $prodiSearchResults = [];

    public function render()
    {
        return $this->renderUserManagement();
    }

    public function saveUser()
    {
        $validated = $this->validate();

        User::updateOrCreate(
            ['id' => $this->editingUserId],
            [
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ]
        );

        $this->reset(['name', 'email', 'editingUserId']);
        session()->flash('success', 'User berhasil disimpan');
    }


}
