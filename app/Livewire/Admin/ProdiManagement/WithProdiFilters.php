<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Prodi;
use Livewire\WithPagination;

trait WithProdiFilters
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public $sortField = 'prodi';

    public $sortDirection = 'asc';

    // public $searchAngkatan = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // public function updatingSearchAngkatan()
    // {
    //     $this->resetPage();
    // }

    // public function inputMainSearch()
    // {
    //     $query = Prodi::query()->with(['jurusan_rel', 'jurusan_rel.fakultas_rel']);
    //     $searchTerm = '%'.$this->search.'%';

    //     // if ($this->switchTable === 'prodi' && ! empty($this->search)) {
    //         if (! empty($this->search)) {
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->where('nama_prodi', 'like', $searchTerm)
    //                 ->orWhere('nama_strata', 'like', $searchTerm)
    //                 ->orWhereHas('jurusan_rel', function ($q) use ($searchTerm) {
    //                     $q->where('nama_jurusan', 'like', $searchTerm)
    //                         ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
    //                         ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
    //                             $sq->where('nama_fakultas', 'like', $searchTerm)
    //                                 ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
    //                         });
    //                 });
    //         });
    //     }

    //     // Filter berdasarkan fakultas yang dipilih
    //     if (! empty($this->selectedFakultasId)) {
    //         $query->whereHas('jurusan_rel', function ($q) {
    //             $q->where('fakultas_id', $this->selectedFakultasId);
    //         });
    //     }

    //     // Filter berdasarkan jurusan yang dipilih
    //     if (! empty($this->selectedJurusanId)) {
    //         $query->where('jurusan_id', $this->selectedJurusanId);
    //     }

    //     $this->sortFieldOrder($query);

    //     return $query;
    // }

    public function inputMainSearch()
    {
        $query = Prodi::query()->with(['jurusan_rel.fakultas_rel']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                // Prodi
                $q->where('nama_prodi', 'like', $searchTerm)
                    ->orWhere('nama_strata', 'like', $searchTerm);

                if (is_numeric($this->search)) {
                    $q->orWhere('prodis.id', $this->search);
                }
                // Jurusan
                $q->orWhereHas('jurusan_rel', function ($jq) use ($searchTerm) {
                    $jq->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm]);
                    // if (is_numeric($this->search)) {
                    //     $jq->orWhere('jurusans.id', $this->search);
                    // }
                });
                // Fakultas
                $q->orWhereHas('jurusan_rel.fakultas_rel', function ($fq) use ($searchTerm) {
                    $fq->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                    // if (is_numeric($this->search)) {
                    //     $fq->orWhere('fakultas.id', $this->search);
                    // }
                });

            });
        }

        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('jurusan_rel', function ($q) {
                $q->where('fakultas_id', $this->selectedFakultasId);
            });
        }

        if (! empty($this->selectedJurusanId)) {
            $query->where('jurusan_id', $this->selectedJurusanId);
        }

        $this->sortFieldOrder($query);

        return $query;
    }

    public function filterBy($strata)
    {
        $this->filter = $strata;
        $this->resetPage();
    }

    public function buttonStrataFilter($query)
    {
        $query->when($this->selectedFakultasId, function ($q) {
            $q->whereHas('jurusan_rel', function ($rel) {
                $rel->where('fakultas_id', $this->selectedFakultasId);
            });
        });

        $query->when($this->selectedJurusanId, function ($q) {
            $q->where('jurusan_id', $this->selectedJurusanId);
        });

        $countQueryBase = clone $query;

        $totalProdis = (clone $countQueryBase)->count();
        $totalSarjana = (clone $countQueryBase)->where('nama_strata', 'Sarjana')->count();
        $totalMagister = (clone $countQueryBase)->where('nama_strata', 'Magister')->count();
        $totalDoktor = (clone $countQueryBase)->where('nama_strata', 'Doktor')->count();

        if ($this->filter === 'sarjana') {
            $query->where('nama_strata', 'Sarjana');
        } elseif ($this->filter === 'magister') {
            $query->where('nama_strata', 'Magister');
        } elseif ($this->filter === 'doktor') {
            $query->where('nama_strata', 'Doktor');
        }

        return [$totalProdis, $totalSarjana, $totalMagister, $totalDoktor];
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filter']);
        // $this->resetFakultasFilter();
        // $this->resetJurusanFilter();
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    // public function resetInputAngkatan()
    // {
    //     $this->reset('searchAngkatan');
    //     $this->resetPage();
    // }

    public function sortFieldOrder($query)
    {
        $query->select('prodis.*');

        if ($this->sortField === 'prodi') {
            $query->orderBy('prodis.nama_prodi', $this->sortDirection);
        } elseif ($this->sortField === 'jurusan') {
            $query->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->orderBy('jurusans.nama_jurusan', $this->sortDirection);
        } elseif ($this->sortField === 'fakultas') {
            $query->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->orderBy('fakultas.nama_fakultas', $this->sortDirection);
        } elseif ($this->sortField === 'strata') {
            $query->orderBy('prodis.nama_strata', $this->sortDirection);
        } else {
            $query->orderBy('prodis.id', $this->sortDirection);
        }

        if ($this->filter === 'jurusan') {
            $query->whereHas('jurusan_rel');
        } elseif ($this->filter === 'fakultas') {
            $query->whereHas('jurusan_rel.fakultas');
        }

        return $query;
    }
}
