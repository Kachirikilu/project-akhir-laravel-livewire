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
        $queryUser = Prodi::query()->with(['jurusan_rel', 'jurusan_rel.fakultas_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $queryUser->searchProdi($search)->get();
        }

        if (! empty($this->selectedJurusanId)) {
            $queryUser->where('jurusan_id', $this->selectedJurusanId);
        }
        if (! empty($this->selectedFakultasId)) {
            $queryUser->whereHas('jurusan_rel', function ($q) {
                $q->where('fakultas_id', $this->selectedFakultasId);
            });
        }

        $this->sortFieldOrderProdi($queryUser);

        return $queryUser;
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

    public function sortFieldOrderProdi($queryUser)
    {
        if ($this->filterPr === 'jurusan') {
            $queryUser->whereHas('jurusan_rel');
        } elseif ($this->filterPr === 'fakultas') {
            $queryUser->whereHas('jurusan_rel.fakultas');
        }

        $primaryTable = $this->switchTable . 's';
        $queryUser->select("$primaryTable.*");

        return match ($this->sortField) {
            'prodi'    => $this->applyProdiNameSort($queryUser),
            'jurusan'  => $queryUser->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                                ->orderBy('jurusans.nama_jurusan', $this->sortDirection),
            'fakultas' => $queryUser->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                                ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                                ->orderBy('fakultas.nama_fakultas', $this->sortDirection),
            'strata'   => $queryUser->orderBy('prodis.nama_strata', $this->sortDirection),
            'kode'     => $this->applyProdiKodeSort($queryUser),
            default    => $queryUser->orderBy("$primaryTable.id", $this->sortDirection),
        };
    }

    private function applyProdiNameSort($queryUser)
    {
        return $queryUser->orderBy('prodis.nama_prodi', $this->sortDirection)
            ->orderByRaw("
                CASE 
                    WHEN nama_strata = 'Sarjana' THEN 1 
                    WHEN nama_strata = 'Magister' THEN 2 
                    WHEN nama_strata = 'Doktor' THEN 3 
                    ELSE 4 
                END " . $this->sortDirection
            );
    }

    private function applyProdiKodeSort($queryUser)
    {
        if (in_array($this->switchTable, ['prodi', 'jurusan'])) {
            $queryUser->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id');
        }

        $orderByRaw = match ($this->switchTable) {
            'prodi'    => "COALESCE(prodis.kode_pr, jurusans.kode_jr, fakultas.kode_fk)",
            'jurusan'  => "COALESCE(jurusans.kode_jr, fakultas.kode_fk)",
            'fakultas' => "fakultas.kode_fk",
            default    => "prodis.id"
        };

        return $queryUser->orderByRaw("$orderByRaw {$this->sortDirection}");
    }
}
