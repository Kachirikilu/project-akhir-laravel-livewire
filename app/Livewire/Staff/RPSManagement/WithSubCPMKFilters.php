<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\SubCPMK;
use Livewire\WithPagination;

trait WithSubCPMKFilters
{
    use WithPagination;

    public $filterSCPMK = '';

    public function inputSCPMKSearch()
    {
        $query = SubCPMK::query()->with(['cpmks.rps.matkul_rel', 'cpmks.rps.matkul_rel.prodis', 'cpmks.rps.matkul_rel.prodis.jurusan_rel', 'cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sub_cpmks.kode_scpmk', 'like', $search)
                ->orwhere('sub_cpmks.deskripsi', 'like', $search)
                    ->orWhere('sub_cpmks.metodologi', 'like', $search)
                  ->orWhere('sub_cpmks.indikator', 'like', $search)
                  ->orWhere('sub_cpmks.metode', 'like', $search)
                  ->orWhere('sub_cpmks.id', 'like', $search);

                  $searchConverted = str_replace(',', '.', $search);
                  $q->orWhere('sub_cpmks.bobot', 'like', '%' . $searchConverted . '%');
            });
        }

        if (! empty($this->selectedProdiId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        $this->sortFieldOrderSCPMK($query);
        return $query;
    }

    public function buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo)
    {
        if ($this->filterSCPMK === 'scpmk-month') {
            $querySCPMK->whereMonth('created_at', $now->month)->whereYear('created_at', $currentYear);
        } elseif ($this->filterSCPMK === 'scpmk-6-months') {
            $querySCPMK->where('created_at', '>=', $sixMonthsAgo);
        } elseif ($this->filterSCPMK === 'scpmk-year') {
            $querySCPMK->whereYear('created_at', $currentYear);
        } elseif ($this->filterSCPMK === 'scpmk-5-years') {
            $querySCPMK->where('created_at', '>=', $fiveYearsAgo);
        } elseif ($this->filterSCPMK === 'scpmk-old') { 
            $querySCPMK->where('created_at', '<', $fiveYearsAgo);
        }
    }

    public function filterBySCPMK($scpmk)
    {
        $this->filterSCPMK = $scpmk;
        $this->resetPage();
    }

    public function sortFieldOrderSCPMK($query)
    {
        $query->select('sub_cpmks.*');

        if ($this->sortField === 'kode') {
            $query->orderBy('kode_scpmk', $this->sortDirection);
        } elseif ($this->sortField === 'indikator') {
            $query->orderBy('indikator', $this->sortDirection);
        } elseif ($this->sortField === 'bobot') {
            $query->orderBy('bobot', $this->sortDirection);
        } else {
            $query->orderBy('sub_cpmks.id', $this->sortDirection);
        }

        return $query;
    }
}
