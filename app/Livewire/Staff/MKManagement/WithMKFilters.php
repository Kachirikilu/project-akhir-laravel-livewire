<?php

namespace App\Livewire\Staff\MKManagement;

use App\Models\Akademik\MataKuliah;
use Livewire\WithPagination;

trait WithMKFilters
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
        $queryMK = MataKuliah::query()
        ->with(['prodis', 'prodis.jr_rel', 'prodis.jr_rel.fk_rel']);
        
        $search = $this->search;

        if (! empty($search)) {
            $queryMK->searchMK($search);
        }

        // Filter Dropdown Silsilah (Tetap di luar closure search)
        if (! empty($this->selectedPrId)) {
            $queryMK->whereHas('prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
        }
        if (! empty($this->selectedJrId)) {
            $queryMK->whereHas('prodis', fn ($q) => $q->where('jr_id', $this->selectedJrId));
        }
        if (! empty($this->selectedFkId)) {
            $queryMK->whereHas('prodis.jr_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
        }

        // Filter Tab/Pills
        // if (! empty($this->filterMK)) {
        //     if (is_numeric($this->filterMK)) {
        //         $queryMK->where('semester', $this->filterMK);
        //     }
        // }

        $this->sortFieldOrderMK($queryMK);

        return $queryMK;
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


   public function sortFieldOrderMK($queryMK)
    {
        $queryMK->select('mata_kuliahs.*');

        return match ($this->sortField) {
            'mk'  => $queryMK->orderBy('nama_mk', $this->sortDirection),
            'semester'=> $queryMK->orderBy('semester', $this->sortDirection),
            'sks'     => $queryMK->orderBy('sks_kuliah', $this->sortDirection),
            'wajib'   => $queryMK->orderBy('is_wajib', $this->sortDirection),
            
            'sks_tm'   => $this->applyMKSksTypeSort($queryMK),
            'sks_pr'   => $this->applyMKSksTypeSort($queryMK),
            'sks_pl'   => $this->applyMKSksTypeSort($queryMK),
            'sks_sm'   => $this->applyMKSksTypeSort($queryMK),
            
            'digit_mk'=> $queryMK->orderBy('digit_mk', $this->sortDirection),
            'created_at' => $queryMK->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryMK->orderBy('updated_at', $this->sortDirection),
            
            'kode' => $this->applyMKKodeSort($queryMK),

            default => $queryMK->orderBy('mata_kuliahs.id', 'desc'),
        };
    }

    private function applyMKSksTypeSort($queryMK)
    {
        $typeMap = [
            'sks_tm' => 1, 
            'sks_pr' => 2, 
            'sks_pl' => 3, 
            'sks_sm' => 4
        ];
        
        $targetType = $typeMap[$this->sortField];

        return $queryMK->orderByRaw("
            CASE WHEN tipe_sks = $targetType THEN sks_kuliah ELSE 0 END $this->sortDirection
        ");
    }

    private function applyMKKodeSort($queryMK)
    {
        return $queryMK->leftJoin('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
            ->leftJoin('prodis', 'prodi_pivot_mk.pr_id', '=', 'prodis.id')
            ->leftJoin('jurusans', 'prodis.jr_id', '=', 'jurusans.id')
            ->leftJoin('fakultas', 'jurusans.fk_id', '=', 'fakultas.id')
            ->groupBy('mata_kuliahs.id')
            ->orderByRaw("
                CONCAT(
                    MAX(CASE 
                        WHEN mata_kuliahs.level_mk = 1 THEN UPPER(mata_kuliahs.kode_mk)
                        WHEN mata_kuliahs.level_mk = 2 THEN COALESCE(prodis.kode_pr, jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.level_mk = 3 THEN COALESCE(jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.level_mk = 4 THEN COALESCE(fakultas.kode_fk, 'UNI')
                        ELSE 'UNI'
                    END), 
                    MAX(mata_kuliahs.digit_semester), 
                    MAX(mata_kuliahs.digit_mk)
                ) $this->sortDirection
            ");
    }
}
