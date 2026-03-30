<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Prodi;
use Livewire\WithPagination;

trait WithProdiFilters
{
    use WithPagination;

    public $search = '';

    public $filterPr = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inputProdiSearch()
    {
        $queryPr = Prodi::query()->with(['jurusan_rel', 'jurusan_rel.fakultas_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $queryPr->searchProdi($search)->get();
        }

        if (! empty($this->selectedJurusanId)) {
            $queryPr->where('jurusan_id', $this->selectedJurusanId);
        }
        if (! empty($this->selectedFakultasId)) {
            $queryPr->whereHas('jurusan_rel', function ($q) {
                $q->where('fakultas_id', $this->selectedFakultasId);
            });
        }

        $this->sortFieldOrderProdi($queryPr);

        return $queryPr;
    }

    public function filterByStrata($strata)
    {
        $this->filterPr = $strata;
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

    public function sortFieldOrderProdi($queryPr)
    {
        if ($this->filterPr === 'jurusan') {
            $queryPr->whereHas('jurusan_rel');
        } elseif ($this->filterPr === 'fakultas') {
            $queryPr->whereHas('jurusan_rel.fakultas');
        }

        $primaryTable = $this->switchTable . 's';
        $queryPr->select("$primaryTable.*");

        return match ($this->sortField) {
            'prodi'    => $this->applyProdiNameSort($queryPr),
            'jurusan'  => $queryPr->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                                ->orderBy('jurusans.nama_jurusan', $this->sortDirection),
            'fakultas' => $queryPr->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                                ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                                ->orderBy('fakultas.nama_fakultas', $this->sortDirection),
            'strata'   => $queryPr->orderBy('prodis.nama_strata', $this->sortDirection),
            'kode'     => $this->applyProdiKodeSort($queryPr),
            default    => $queryPr->orderBy("$primaryTable.id", $this->sortDirection),
        };
    }

    private function applyProdiNameSort($queryPr)
    {
        return $queryPr->orderBy('prodis.nama_prodi', $this->sortDirection)
            ->orderByRaw("
                CASE 
                    WHEN nama_strata = 'Sarjana' THEN 1 
                    WHEN nama_strata = 'Magister' THEN 2 
                    WHEN nama_strata = 'Doktor' THEN 3 
                    ELSE 4 
                END " . $this->sortDirection
            );
    }

    private function applyProdiKodeSort($queryPr)
    {
        $queryPr->select('prodis.*')
            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id');;

        return match ($this->sortField) {
            'kode'  => $queryPr->orderBy('kode_pr', $this->sortDirection)
                        ->orderBy('jurusans.kode_jr', $this->sortDirection)
                            ->orderBy('fakultas.kode_fk', $this->sortDirection),
            'prodis'  => $queryPr->orderBy('nama_prodi', $this->sortDirection),
            'jurusan'  => $queryPr->orderBy('jurusans.nama_jurusan', $this->sortDirection),
            'fakultas' => $queryPr->orderBy('fakultas.nama_fakultas', $this->sortDirection),
            'created_at' => $queryPr->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryPr->orderBy('updated_at', $this->sortDirection),
            default    => $queryPr->orderBy('id', 'desc'),
        };
    }
}
