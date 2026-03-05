<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Fakultas;
use Livewire\WithPagination;

trait WithFakultasFilters
{
    use WithPagination;

    // public $search = '';

    // public $filter = '';

    // public $sortField = 'prodi';

    // public $sortDirection = 'asc';

    // public function updatingSearch()
    // {
    //     $this->resetPage();
    // }

    // public function updatingSearchAngkatan()
    // {
    //     $this->resetPage();
    // }

    public function inputFakultasSearch()
    {
        $query = Fakultas::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama_fakultas', 'like', "%{$this->search}%")
                    ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", ["%{$this->search}%"]);
            });
        }

        if (! empty($this->selectedFakultasId)) {
            $query->where('fakultas_id', $this->selectedFakultasId);
        }

        $this->sortFieldOrderFakultas($query);

        return $query;
    }

    // public function filterBy($strata)
    // {
    //     $this->filter = $strata;
    //     $this->resetPage();
    // }

    // public function resetInputFilter()
    // {
    //     $this->reset(['search', 'filter']);
    //     $this->resetFakultasFilter();
    //     $this->resetPage();
    // }

    // public function sortBy($field)
    // {
    //     if ($this->sortField === $field) {
    //         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    //     } else {
    //         $this->sortField = $field;
    //         $this->sortDirection = 'asc';
    //     }
    //     $this->resetPage();
    // }

    public function sortFieldOrderFakultas($query)
    {
        $query->select('fakultas.*');

        if ($this->sortField === 'fakultas') {
            $query->orderBy('nama_fakultas', $this->sortDirection);
        } else {
            $query->orderBy('id', $this->sortDirection);
        }
        return $query;
    }
}