<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\CPL;
use Livewire\WithPagination;

trait WithCPLFilters
{
    use WithPagination;

    public $filterCPL = '';

    public function inputCPLSearch()
    {
        $queryCPL = CPL::query()->with(['rps.matkul_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis.jurusan_rel.fakultas_rel',
                                        'cpmks.rps.matkul_rel', 'cpmks.rps.matkul_rel.prodis', 'cpmks.rps.matkul_rel.prodis.jurusan_rel', 'cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $queryCPL->searchCPL($search);
        }

        if (! empty($this->selectedProdiId)) {
            $queryCPL->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $queryCPL->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $queryCPL->whereHas('rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }
        if (! empty($this->selectedMatkulId)) {
            $queryCPL->whereHas('rps', fn ($q) => $q->where('mk_id', $this->selectedMatkulId));
        }
        if (! empty($this->selectedRPSId)) {
            $queryCPL->whereHas('rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
        }

        if (! empty($this->selectedProdiId)) {
            $queryCPL->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $queryCPL->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $queryCPL->whereHas('cpmks.rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }
        if (! empty($this->selectedMatkulId)) {
            $queryCPL->whereHas('cpmks.rps', fn ($q) => $q->where('mk_id', $this->selectedMatkulId));
        }
        if (! empty($this->selectedRPSId)) {
            $queryCPL->whereHas('cpmks.rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
        }
        if (! empty($this->selectedCPMKId)) {
            $queryCPL->whereHas('cpmks', fn ($q) => $q->where('cpmks.id', $this->selectedCPMKId));
        }

        $this->sortFieldOrderCPL($queryCPL);

        return $queryCPL;
    }

    public function buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo)
    {
        if ($this->filterCPL === 'cpl-month') {
            $queryCPL->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPL === 'cpl-6-months') {
            $queryCPL->where('created_at', '>=', $sixMonthsAgo);
        } elseif ($this->filterCPL === 'cpl-year') {
            $queryCPL->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPL === 'cpl-5-years') {
            $queryCPL->where('created_at', '<=', $fiveYearsAgo);
        } elseif ($this->filterCPL === 'cpl-old') {
            $queryCPL->where('created_at', '<', $fiveYearsAgo);
        }
    }

    public function filterByCPL($cpl)
    {
        $this->filterCPL = $cpl;
        $this->resetPage();
    }

    public function sortFieldOrderCPL($queryCPL)
    {
        $queryCPL->select('cpls.*');

        return match ($this->sortField) {
            'kode'   => $queryCPL->orderBy('kode_cpl', $this->sortDirection),
            
            'deskripsi' => $queryCPL->orderBy('deskripsi', $this->sortDirection),
            'created_at' => $queryCPL->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryCPL->orderBy('updated_at', $this->sortDirection),
            
            default => $queryCPL->orderBy('id', $this->sortDirection),
        };

        return $queryCPL;
    }
}
