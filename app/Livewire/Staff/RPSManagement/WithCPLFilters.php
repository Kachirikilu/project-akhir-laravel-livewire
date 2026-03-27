<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\CPL;
use Livewire\WithPagination;

trait WithCPLFilters
{
    use WithPagination;

  public function inputCPLSearch()
    {
        $query = CPL::query();
        $searchTerm = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where('kode_cpl', 'like', $searchTerm)
                  ->orWhere('deskripsi', 'like', $searchTerm);
        }

        $this->sortFieldOrderCPL($query);

        return $query;
    }

    public function sortFieldOrderCPL($query)
    {
        $query->select('cpls.*');

        if ($this->sortField === 'kode') {
            $query->orderBy('kode_cpl', $this->sortDirection);
        } elseif ($this->sortField === 'aspek') {
            $query->orderBy('aspek', $this->sortDirection);
        } elseif ($this->sortField === 'deskripsi') {
            $query->orderBy('deskripsi', $this->sortDirection);
        } else {
            $query->orderBy('cpls.id', 'desc');
        }

        return $query;
    }
}
