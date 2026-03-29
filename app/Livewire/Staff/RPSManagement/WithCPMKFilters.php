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
        $query = CPMK::query()
        ->with(['rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel']);
        $search = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where(function ($q) use ($search) {
                $q->where('cpmks.kode_cpmk', 'like', $search)
                  ->orWhere('cpmks.deskripsi', 'like', $search)
                  ->orWhere('cpmks.id', 'like', $search);
            });
        }

        if (! empty($this->selectedProdiId)) {
            $query->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        $this->sortFieldOrderCPMK($query);
        return $query;
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

        if ($this->sortField === 'kode') {
            $queryCPMK->orderBy('kode_cpmk', $this->sortDirection);
        } elseif ($this->sortField === 'deskripsi') {
            $queryCPMK->orderBy('deskripsi', $this->sortDirection);
        } else {
            $queryCPMK->orderBy('cpmks.id', $this->sortDirection);
        }

        return $queryCPMK;
    }
}
