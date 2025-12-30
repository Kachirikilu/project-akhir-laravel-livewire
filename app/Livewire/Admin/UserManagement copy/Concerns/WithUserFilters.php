<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

trait WithUserFilters
{
    public function filterBy($role)
    {
        $this->filter = $role;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filter']);
        $this->resetPage();
    }
}
