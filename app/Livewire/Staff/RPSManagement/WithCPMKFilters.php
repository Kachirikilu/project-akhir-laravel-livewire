<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\CPMK;
use Livewire\WithPagination;

trait WithCPMKFilters
{
    use WithPagination;

    public $filterCPMK = '';

    public function inputCPMKSearch()
    {
        $queryCPMK = CPMK::query()->with(['rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $queryCPMK->searchCPMK($search);
        }

        if (! empty($this->selectedProdiId)) {
            $queryCPMK->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $queryCPMK->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $queryCPMK->whereHas('rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }
        if (! empty($this->selectedMatkulId)) {
            $queryCPMK->whereHas('rps', fn ($q) => $q->where('mk_id', $this->selectedMatkulId));
        }

        $this->sortFieldOrderCPMK($queryCPMK);
        return $queryCPMK;
    }


    public function buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo)
    {
        // dd($this->filterCPMK);
        if ($this->filterCPMK === 'cpmk-month') {
            $queryCPMK->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPMK === 'cpmk-6-months') {
            $queryCPMK->where('created_at', '>=', $sixMonthsAgo);
        } elseif ($this->filterCPMK === 'cpmk-year') {
            $queryCPMK->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPMK === 'cpmk-5-years') {
            $queryCPMK->where('created_at', '>=', $fiveYearsAgo);
        } elseif ($this->filterCPMK === 'cpmk-old') {
            $queryCPMK->where('created_at', '<', $fiveYearsAgo);
        }
    }

    public function filterByCPMK($cpmk)
    {
        $this->filterCPMK = $cpmk;
        $this->resetPage();
    }

    public function sortFieldOrderCPMK($queryCPMK)
    {
        $queryCPMK->select('cpmks.*');


        return match ($this->sortField) {
            'kode'   => $queryCPMK->orderBy('kode_cpmk', $this->sortDirection),
            
            'deskripsi' => $queryCPMK->orderBy('deskripsi', $this->sortDirection),
            'created_at' => $queryCPMK->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryCPMK->orderBy('updated_at', $this->sortDirection),
            
            default => $queryCPMK->orderBy('id', $this->sortDirection),
        };

        return $queryCPMK;
    }
}
