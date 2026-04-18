<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Auth\Dosen;
use Livewire\WithPagination;

trait WithDosenFilters
{
    use WithPagination;

    public function inputDosenSearch()
    {
        if ($this->switchTable === 'dosen') {

            $query = Dosen::query();
            $search = '%'.trim($this->search).'%';

            if (! empty($this->search)) {
                $query->where('name', 'like', $search)
                    ->orWhere('nip', 'like', $search)
                    ->orWhere('nidn', 'like', $search)
                    ->orWhere('dosens.id', 'like', $search);
            }

            $this->sortFieldOrderDosen($query);

            return $query;
        }
    }

    public function sortFieldOrderDosen($query)
    {
        $query->select('dosens.*');

        if ($this->sortField === 'nama') {
            $query->orderBy('name', $this->sortDirection);
        } elseif ($this->sortField === 'nip') {
            $query->orderBy('nip', $this->sortDirection);
        } elseif ($this->sortField === 'nidn') {
            $query->orderBy('nidn', $this->sortDirection);
        } else {
            $query->orderBy('dosens.id', $this->sortDirection);
        }

        return $query;
    }
}
