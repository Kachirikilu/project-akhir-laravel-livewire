<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\SubCPMK;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

trait WithSubCPMKFilters
{
    use WithPagination;

    public $filterSCPMK = '';

    public function inputSCPMKSearch()
    {
        $querySCPMK = SubCPMK::query()->with(['cpmks.rps.matkul_rel', 'cpmks.rps.matkul_rel.prodis', 'cpmks.rps.matkul_rel.prodis.jurusan_rel', 'cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $querySCPMK->searchSCPMK($search);
        }

        if (! empty($this->selectedProdiId)) {
            $querySCPMK->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $querySCPMK->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $querySCPMK->whereHas('cpmks.rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }
        if (! empty($this->selectedMatkulId)) {
            $querySCPMK->whereHas('cpmks.rps', fn ($q) => $q->where('mk_id', $this->selectedMatkulId));
        }
        if (! empty($this->selectedRPSId)) {
            $querySCPMK->whereHas('cpmks.rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
        }
        if (! empty($this->selectedCPMKId)) {
            $querySCPMK->whereHas('cpmks', fn ($q) => $q->where('cpmks.id', $this->selectedCPMKId));
        }
        

        $this->sortFieldOrderSCPMK($querySCPMK);
        return $querySCPMK;
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

    public function sortFieldOrderSCPMK($querySCPMK)
    {
        $querySCPMK->select('sub_cpmks.*');

        return match ($this->sortField) {
            'kode'   => $querySCPMK->orderBy('kode_scpmk', $this->sortDirection),
            
            'deskripsi' => $querySCPMK->orderBy('deskripsi', $this->sortDirection),
            'metodologi' => $querySCPMK->orderBy('metodologi', $this->sortDirection),
            'indikator' => $querySCPMK->orderBy('indikator', $this->sortDirection),
            'metode' => $querySCPMK->orderBy('metode', $this->sortDirection),
            'bobot' => $querySCPMK->orderBy('bobot', $this->sortDirection),
            'tugas' => $querySCPMK->orderBy('deskripsi_tugas', $this->sortDirection),
            'tugas' => $querySCPMK->orderBy('waktu_tugas', $this->sortDirection),
            'mandiri' => $querySCPMK->orderBy('waktu_mandiri', $this->sortDirection),

            'created_at' => $querySCPMK->orderBy('created_at', $this->sortDirection),
            'updated_at' => $querySCPMK->orderBy('updated_at', $this->sortDirection),
            
            default => $querySCPMK->orderBy('id', $this->sortDirection),
        };

        return $querySCPMK;
    }
}
