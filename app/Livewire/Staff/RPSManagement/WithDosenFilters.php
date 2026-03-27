<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Auth\Dosen;
use Livewire\WithPagination;

trait WithDosenFilters
{
    use WithPagination;

    public function inputDosenSearch()
    {
        $query = Dosen::query();
        $searchTerm = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where('name', 'like', $searchTerm)
                  ->orWhere('nip', 'like', $searchTerm)
                  ->orWhere('nidn', 'like', $searchTerm);
        }

        $this->sortFieldOrderDosen($query);
        return $query;
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
            $query->orderBy('dosens.id', 'desc');
        }

        return $query;
    }
}
