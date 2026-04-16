<?php

namespace App\Livewire\Staff\ReferensiManagement;

use App\Models\Akademik\Referensi;
use Livewire\WithPagination;

trait WithRefFilters
{
    use WithPagination;

    public $filterRef = '';

    public function inputRefSearch()
    {
        $queryRef = Referensi::query()->with([
            'rps.mk_rel', 'rps.mk_rel.prodis', 'rps.mk_rel.prodis.jr_rel', 'rps.mk_rel.prodis.jr_rel.fk_rel',
            'cpmks.rps.mk_rel', 'cpmks.rps.mk_rel.prodis', 'cpmks.rps.mk_rel.prodis.jr_rel', 'cpmks.rps.mk_rel.prodis.jr_rel.fk_rel',
            'scpmks.cpmks.rps.mk_rel', 'scpmks.cpmks.rps.mk_rel.prodis', 'scpmks.cpmks.rps.mk_rel.prodis.jr_rel', 'scpmks.cpmks.rps.mk_rel.prodis.jr_rel.fk_rel'

            ]);
        $search = $this->search;

        if (! empty($search)) {
            $queryRef->searchRef($search);
        }


        if (! empty($this->selectedPrId)) {
            $queryRef->where(function ($q) {
                $q->whereRelation('rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId)
                ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId)
                ->orWhereRelation('scpmks.cpmks.rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId);
            });
        }
        if (! empty($this->selectedJrId)) {
            $queryRef->where(function ($q) {
                $q->whereRelation('rps.mk_rel.prodis', 'jr_id', $this->selectedJrId)
                ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'jr_id', $this->selectedJrId)
                ->orWhereRelation('scpmks.cpmks.rps.mk_rel.prodis', 'jr_id', $this->selectedJrId);
            });
        }
        if (! empty($this->selectedFkId)) {
            $queryRef->where(function ($q) {
                $q->whereRelation('rps.mk_rel.prodis.jr_rel', 'fk_id', $this->selectedFkId)
                ->orWhereRelation('cpmks.rps.mk_rel.prodis.jr_rel', 'fk_id', $this->selectedFkId)
                ->orWhereRelation('scpmks.cpmks.rps.mk_rel.prodis.jr_rel', 'fk_id', $this->selectedFkId);
            });
        }
        if (! empty($this->selectedMKId)) {
            $queryRef->where(function ($q) {
                $q->whereRelation('rps', 'mk_id', $this->selectedMKId)
                ->orWhereRelation('cpmks.rps', 'mk_id', $this->selectedMKId)
                ->orWhereRelation('scpmks.cpmks.rps', 'mk_id', $this->selectedMKId);
            });
        }
        if (! empty($this->selectedRPSId)) {
            $queryRef->where(function ($q) {
                $q->whereRelation('rps', 'rps.id', $this->selectedRPSId)
                ->orWhereRelation('cpmks.rps', 'rps.id', $this->selectedRPSId)
                ->orWhereRelation('scpmks.cpmks.rps', 'rps.id', $this->selectedRPSId);
            });
        }

        if (! empty($this->selectedCPMKId)) {
            $queryRef->where(function ($q) {
                $q->whereRelation('cpmks', 'cpmks.id', $this->selectedCPMKId)
                ->orWhereRelation('scpmks.cpmks', 'cpmks.id', $this->selectedCPMKId);
            });
        }

        if (! empty($this->selectedSCPMKId)) {
            $queryRef->whereHas('scpmks', fn ($q) => $q->where('sub_cpmks.id', $this->selectedSCPMKId));
        }

        $this->sortFieldOrderRef($queryRef);

        return $queryRef;
    }

    public function buttonRefFilter($queryRef, $currentYear, $threeYearsAgo, $fiveYearsAgo, $tenYearsAgo)
    {
        if ($this->filterRef === 'ref-year') {
            $queryRef->where('tahun', $currentYear);
        } elseif ($this->filterRef === 'ref-3-years') {
            $queryRef->where('tahun', '>=', $threeYearsAgo)
                    ->where('tahun', '<', $currentYear);
        } elseif ($this->filterRef === 'ref-5-years') {
            $queryRef->where('tahun', '>=', $fiveYearsAgo)
                    ->where('tahun', '<', $threeYearsAgo);
        } elseif ($this->filterRef === 'ref-10-years') {
            $queryRef->where('tahun', '>=', $tenYearsAgo)
                    ->where('tahun', '<', $fiveYearsAgo);
        } elseif ($this->filterRef === 'ref-old') {
            $queryRef->where('tahun', '<', $tenYearsAgo);
        }
    }

    public function filterByRef($ref)
    {
        $this->filterRef = $ref;
        $this->resetPage();
    }

    public function sortFieldOrderRef($queryRef)
    {
        $queryRef->select('referensis.*');

        return match ($this->sortField) {
            'kode'   => $queryRef->orderBy('kode_ref', $this->sortDirection),
            
            'judul' => $queryRef->orderBy('judul', $this->sortDirection),
            'penulis' => $queryRef->orderBy('penulis', $this->sortDirection),
            'penerbit' => $queryRef->orderBy('penerbit', $this->sortDirection),
            'tahun' => $queryRef->orderBy('tahun', $this->sortDirection),
            'link' => $queryRef->orderBy('link', $this->sortDirection),

            'created_at' => $queryRef->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryRef->orderBy('updated_at', $this->sortDirection),
            
            default => $queryRef->orderBy('id', $this->sortDirection),
        };

        return $queryRef;
    }
}
