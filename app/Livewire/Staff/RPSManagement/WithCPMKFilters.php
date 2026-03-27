<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\CPMK;
use Livewire\WithPagination;

trait WithCPMKFilters
{
    use WithPagination;

    public function inputCPMKSearch()
    {
        $query = CPMK::query();
        $searchTerm = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode_cpmk', 'like', $searchTerm)
                  ->orWhere('digit_cpmk', 'like', $searchTerm)
                  ->orWhere('deskripsi', 'like', $searchTerm);
            });
        }

        $this->sortFieldOrderCPMK($query);
        return $query;
    }

    public function sortFieldOrderCPMK($query)
    {
        $query->select('cpmks.*');

        if ($this->sortField === 'kode') {
            $query->orderBy('kode_cpmk', $this->sortDirection)
                  ->orderBy('digit_cpmk', $this->sortDirection);

        } elseif ($this->sortField === 'deskripsi') {
            $query->orderBy('deskripsi', $this->sortDirection);

        } elseif ($this->sortField === 'matkul') {
            $query->leftJoin('mata_kuliahs', 'cpmks.matkul_id', '=', 'mata_kuliahs.id')
                  ->orderBy('mata_kuliahs.nama_matkul', $this->sortDirection);

        } else {
            $query->orderBy('cpmks.id', 'desc');
        }

        return $query;
    }
}
