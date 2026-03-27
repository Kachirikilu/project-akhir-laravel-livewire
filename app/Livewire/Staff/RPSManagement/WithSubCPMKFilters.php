<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\SubCPMK;
use Livewire\WithPagination;

trait WithSubCPMKFilters
{
    use WithPagination;

   public function inputSCPMKSearch()
    {
        $query = SubCPMK::query();
        $searchTerm = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode_scpmk', 'like', $searchTerm)
                  ->orWhere('digit_scpmk', 'like', $searchTerm)
                  ->orWhere('indikator', 'like', $searchTerm);
            });
        }

        $this->sortFieldOrderSCPMK($query);
        return $query;
    }

    public function sortFieldOrderSCPMK($query)
    {
        $query->select('sub_cpmks.*');

        if ($this->sortField === 'kode') {
            $query->orderBy('kode_scpmk', $this->sortDirection)
                ->orderBy('digit_scpmk', $this->sortDirection);
        } elseif ($this->sortField === 'indikator') {
            $query->orderBy('indikator', $this->sortDirection);
        } elseif ($this->sortField === 'bobot') {
            $query->orderBy('bobot', $this->sortDirection);
        } else {
            $query->orderBy('sub_cpmks.id', 'desc');
        }

        return $query;
    }
}
