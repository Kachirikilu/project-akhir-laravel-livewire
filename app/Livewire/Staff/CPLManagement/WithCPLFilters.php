<?php

namespace App\Livewire\Staff\CPLManagement;

use App\Models\Akademik\CPL;
use Livewire\WithPagination;

trait WithCPLFilters
{
    use WithPagination;

    public $filterCPL = '';

    public function inputCPLSearch()
    {
        $queryCPL = CPL::query()->with(['rps.mk_rel', 'rps.mk_rel.prodis', 'rps.mk_rel.prodis.jr_rel', 'rps.mk_rel.prodis.jr_rel.fk_rel',
                                        'cpmks.rps.mk_rel', 'cpmks.rps.mk_rel.prodis', 'cpmks.rps.mk_rel.prodis.jr_rel', 'cpmks.rps.mk_rel.prodis.jr_rel.fk_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $queryCPL->searchCPL($search);
        }

        if (! empty($this->selectedPrId)) {
            $queryCPL->whereHas('rps.mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
        }
        if (! empty($this->selectedJrId)) {
            $queryCPL->whereHas('rps.mk_rel.prodis', fn ($q) => $q->where('jr_id', $this->selectedJrId));
        }
        if (! empty($this->selectedFkId)) {
            $queryCPL->whereHas('rps.mk_rel.prodis.jr_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
        }
        if (! empty($this->selectedMKId)) {
            $queryCPL->whereHas('rps', fn ($q) => $q->where('mk_id', $this->selectedMKId));
        }
        if (! empty($this->selectedRPSId)) {
            $queryCPL->whereHas('rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
        }

        if (! empty($this->selectedPrId)) {
            $queryCPL->whereHas('cpmks.rps.mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
        }
        if (! empty($this->selectedJrId)) {
            $queryCPL->whereHas('cpmks.rps.mk_rel.prodis', fn ($q) => $q->where('jr_id', $this->selectedJrId));
        }
        if (! empty($this->selectedFkId)) {
            $queryCPL->whereHas('cpmks.rps.mk_rel.prodis.jr_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
        }
        if (! empty($this->selectedMKId)) {
            $queryCPL->whereHas('cpmks.rps', fn ($q) => $q->where('mk_id', $this->selectedMKId));
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
