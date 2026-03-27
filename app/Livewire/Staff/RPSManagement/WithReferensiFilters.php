<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\Referensi;
use Livewire\WithPagination;

trait WithReferensiFilters
{
    use WithPagination;

  public function inputRefSearch()
    {
        $query = Referensi::query();
        $searchTerm = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where('judul', 'like', $searchTerm)
                  ->orWhere('penulis', 'like', $searchTerm)
                  ->orWhere('tahun', 'like', $searchTerm);
        }

        $this->sortFieldOrderRef($query);

        return $query;
    }

   public function sortFieldOrderRef($query)
    {
        $query->select('referensis.*');

        if ($this->sortField === 'judul') {
            $query->orderBy('judul', $this->sortDirection);
        } elseif ($this->sortField === 'penulis') {
            $query->orderBy('penulis', $this->sortDirection);
        } elseif ($this->sortField === 'tahun') {
            $query->orderBy('tahun', $this->sortDirection);
        } elseif ($this->sortField === 'jenis') {
            $query->orderBy('jenis_referensi', $this->sortDirection);
        } else {
            $query->orderBy('referensis.id', 'desc');
        }

        return $query;
    }
}
