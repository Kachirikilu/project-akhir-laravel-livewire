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

    // public function inputFakultasSearch()
    // {
    //     $query = Fakultas::query();
    //     $searchTerm = '%'.$this->search.'%';

    //     // if ($this->switchTable === 'fakultas' && ! empty($this->search)) {
    //     if (! empty($this->search)) {
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->where('nama_fakultas', 'like', $searchTerm)
    //                 ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
    //         });
    //     }

    //     if (! empty($this->selectedFakultasId)) {
    //         $query->where('id', $this->selectedFakultasId);
    //     }

    //     if (! empty($this->selectedJurusanId)) {
    //         $query->where('jurusan_rel.id', $this->selectedJurusanId);
    //     }

    //     $this->sortFieldOrderFakultas($query);

    //     return $query;
    // }

    // public function inputFakultasSearch()
    // {
    //     $query = Fakultas::query();
    //     $searchTerm = '%'.$this->search.'%';

    //     if (! empty($this->search)) {
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->where('nama_fakultas', 'like', $searchTerm)
    //                 ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
    //         });
    //     }

    //     if (! empty($this->selectedFakultasId)) {
    //         $query->where('id', $this->selectedFakultasId);
    //     }

    //     if (! empty($this->selectedJurusanId)) {
    //         $query->whereHas('jurusans', function ($q) {
    //             $q->where('id', $this->selectedJurusanId);
    //         });
    //     }

    //     $this->sortFieldOrderFakultas($query);

    //     return $query;
    // }

    public function inputFakultasSearch()
    {
        $query = Fakultas::query()->with(['jurusans.prodis']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_fakultas', 'like', $searchTerm);
                if (is_numeric($this->search)) {
                    $q->orWhere('id', $this->search);
                }
                $q->orWhereHas('jurusans', function ($jq) use ($searchTerm) {
                    $jq->where('nama_jurusan', 'like', $searchTerm);
                    // if (is_numeric($this->search)) {
                    //     $jq->orWhere('id', $this->search);
                    // }
                });
                $q->orWhereHas('jurusans.prodis', function ($pq) use ($searchTerm) {
                    $pq->where('nama_prodi', 'like', $searchTerm);
                    // if (is_numeric($this->search)) {
                    //     $pq->orWhere('id', $this->search);
                    // }
                });

            });
        }

        if (! empty($this->selectedFakultasId)) {
            $query->where('id', $this->selectedFakultasId);
        }

        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('jurusans', function ($q) {
                $q->where('id', $this->selectedJurusanId);
            });
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
