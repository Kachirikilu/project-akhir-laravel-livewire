<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\Referensi;
use Livewire\WithPagination;

trait WithReferensiFilters
{
    use WithPagination;

    public $filterRef = '';

    public function inputRefSearch()
    {
        $queryRef = Referensi::query()->with(['rps.matkul_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $queryRef->searchRef($search);
        }

        if (! empty($this->selectedProdiId)) {
            $queryRef->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $queryRef->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $queryRef->whereHas('rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
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
