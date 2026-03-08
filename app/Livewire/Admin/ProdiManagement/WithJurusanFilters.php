<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Jurusan;
use Livewire\WithPagination;

trait WithJurusanFilters
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

    // public function inputJurusanSearch()
    // {
    //     $query = Jurusan::query()->with(['fakultas_rel']);
    //     $searchTerm = '%'.$this->search.'%';

    //     if (! empty($this->search)) {
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->where('jurusans.nama_jurusan', 'like', $searchTerm)
    //                 ->orWhere('jurusans.id', $this->search)
    //                 ->orWhereRaw("CONCAT('Jurusan ', jurusans.nama_jurusan) LIKE ?", [$searchTerm])
    //                 ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
    //                     $sq->where('nama_fakultas', 'like', $searchTerm)
    //                         ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
    //                 });
    //         });
    //     }

    //     if (! empty($this->selectedFakultasId)) {
    //         $query->where('jurusans.fakultas_id', $this->selectedFakultasId);
    //     }

    //     if (! empty($this->selectedJurusanId)) {
    //         $query->where('jurusans.id', $this->selectedJurusanId);
    //     }

    //     $this->sortFieldOrderJurusan($query);

    //     return $query;
    // }

    public function inputJurusanSearch()
    {
        $query = Jurusan::query()->with(['fakultas_rel', 'prodis']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('jurusans.nama_jurusan', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT('Jurusan ', jurusans.nama_jurusan) LIKE ?", [$searchTerm]);
                if (is_numeric($this->search)) {
                    $q->orWhere('jurusans.id', $this->search);
                }
                $q->orWhereHas('fakultas_rel', function ($fq) use ($searchTerm) {
                    $fq->where('nama_fakultas', 'like', $searchTerm);
                    // if (is_numeric($this->search)) {
                    //     $fq->orWhere('id', $this->search);
                    // }
                });
                $q->orWhereHas('prodis', function ($pq) use ($searchTerm) {
                    $pq->where('nama_prodi', 'like', $searchTerm);
                    // if (is_numeric($this->search)) {
                    //     $pq->orWhere('id', $this->search);
                    // }
                });

            });
        }

        if (! empty($this->selectedFakultasId)) {
            $query->where('jurusans.fakultas_id', $this->selectedFakultasId);
        }

        if (! empty($this->selectedJurusanId)) {
            $query->where('jurusans.id', $this->selectedJurusanId);
        }

        $this->sortFieldOrderJurusan($query);

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

    public function sortFieldOrderJurusan($query)
    {
        $query->select('jurusans.*');

        if ($this->sortField === 'jurusan') {
            $query->orderBy('jurusans.nama_jurusan', $this->sortDirection);

        } elseif ($this->sortField === 'fakultas') {
            $query->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->orderBy('fakultas.nama_fakultas', $this->sortDirection);

        } else {
            $query->orderBy('jurusans.id', $this->sortDirection);
        }

        return $query;
    }
}
