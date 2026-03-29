<?php

namespace App\Livewire\Staff\MatkulManagement;


use App\Models\Akademik\MataKuliah;
use Livewire\WithPagination;

trait WithMatkulFilters
{
    use WithPagination;

    public $search = '';

    public $filterMK = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inputMKSearch()
    {
        $query = MataKuliah::query()
        // ->select('mata_kuliahs.*')
        ->with(['prodis', 'prodis.jurusan_rel', 'prodis.jurusan_rel.fakultas_rel']);
        // ->distinct();
        $search = $this->search;

        if (! empty($search)) {
            $query->searchMK($search);
        }

        // Filter Dropdown Silsilah (Tetap di luar closure search)
        if (! empty($this->selectedProdiId)) {
            $query->whereHas('prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        // Filter Tab/Pills
        // if (! empty($this->filterMK)) {
        //     if (is_numeric($this->filterMK)) {
        //         $query->where('semester', $this->filterMK);
        //     }
        // }

        $this->sortFieldOrderMK($query);

        return $query;
    }

    public function filterByMK($mk)
    {
        $this->filterMK = $mk;
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


   public function sortFieldOrderMK($query)
    {
        $query->select('mata_kuliahs.*');

        return match (true) {
            $this->sortField === 'matkul'  => $query->orderBy('nama_matkul', $this->sortDirection),
            $this->sortField === 'semester'=> $query->orderBy('semester', $this->sortDirection),
            $this->sortField === 'sks'     => $query->orderBy('sks_kuliah', $this->sortDirection),
            $this->sortField === 'wajib'   => $query->orderBy('is_wajib', $this->sortDirection),
            $this->sortField === 'digit_mk'=> $query->orderBy('digit_mk', $this->sortDirection),
            
            in_array($this->sortField, ['sks_tm', 'sks_pr', 'sks_pl', 'sks_sm']) 
                => $this->applyMKSksTypeSort($query),
            
                $this->sortField === 'kode' 
                => $this->applyMKKodeSort($query),

            default => $query->orderBy('mata_kuliahs.id', 'desc'),
        };
    }

    private function applyMKSksTypeSort($query)
    {
        $typeMap = [
            'sks_tm' => 1, 
            'sks_pr' => 2, 
            'sks_pl' => 3, 
            'sks_sm' => 4
        ];
        
        $targetType = $typeMap[$this->sortField];

        return $query->orderByRaw("
            CASE WHEN tipe_sks = $targetType THEN sks_kuliah ELSE 0 END $this->sortDirection
        ");
    }

    private function applyMKKodeSort($query)
    {
        return $query->leftJoin('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
            ->leftJoin('prodis', 'prodi_pivot_mk.prodi_id', '=', 'prodis.id')
            ->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
            ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
            ->groupBy('mata_kuliahs.id')
            ->orderByRaw("
                CONCAT(
                    MAX(CASE 
                        WHEN mata_kuliahs.tingkatan_mk = 1 THEN UPPER(mata_kuliahs.kode_mk)
                        WHEN mata_kuliahs.tingkatan_mk = 2 THEN COALESCE(prodis.kode_pr, jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.tingkatan_mk = 3 THEN COALESCE(jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.tingkatan_mk = 4 THEN COALESCE(fakultas.kode_fk, 'UNI')
                        ELSE 'UNI'
                    END), 
                    MAX(mata_kuliahs.digit_semester), 
                    MAX(mata_kuliahs.digit_mk)
                ) $this->sortDirection
            ");
    }
}
