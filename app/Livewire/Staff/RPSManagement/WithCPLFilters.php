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
        $query = CPL::query()->with(['cpmks.rps.matkul_rel', 'cpmks.rps.matkul_rel.prodis', 'cpmks.rps.matkul_rel.prodis.jurusan_rel', 'cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where('kode_cpl', 'like', $search)
                  ->orWhere('deskripsi', 'like', $search)
                  ->orWhere('cpls.id', 'like', $search);
        }

        $this->sortFieldOrderCPL($query);

        if (! empty($this->selectedProdiId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        return $query;
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

    public function sortFieldOrderCPL($query)
    {
        $query->select('cpls.*');

        if ($this->sortField === 'kode') {
            $query->orderBy('kode_cpl', $this->sortDirection);
        } elseif ($this->sortField === 'deskripsi') {
            $query->orderBy('deskripsi', $this->sortDirection);
        } else {
            $query->orderBy('cpls.id', $this->sortDirection);
        }

        return $query;
    }
}
