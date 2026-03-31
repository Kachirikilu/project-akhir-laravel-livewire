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
        $queryRef = Referensi::query()->with([
            'rps.matkul_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis.jurusan_rel.fakultas_rel',
            'cpmks.rps.matkul_rel', 'cpmks.rps.matkul_rel.prodis', 'cpmks.rps.matkul_rel.prodis.jurusan_rel', 'cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel',
            'scpmks.cpmks.rps.matkul_rel', 'scpmks.cpmks.rps.matkul_rel.prodis', 'scpmks.cpmks.rps.matkul_rel.prodis.jurusan_rel', 'scpmks.cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel'

            ]);
        $search = $this->search;

        if (! empty($search)) {
            $queryRef->searchRef($search);
        }

        if (! empty($this->selectedProdiId)) {
            $queryRef->where(function ($query) {$query->whereHas('rps.matkul_rel.prodis', function ($q) { $q->where('prodis.id', $this->selectedProdiId);
            })->orWhereHas('cpmks.rps.matkul_rel.prodis', function ($q) { $q->where('prodis.id', $this->selectedProdiId);})
            ->orWhereHas('scpmks.cpmks.rps.matkul_rel.prodis', function ($q) { $q->where('prodis.id', $this->selectedProdiId);}); });
        }
        if (! empty($this->selectedJurusanId)) {
            $queryRef->where(function ($query) {$query->whereHas('rps.matkul_rel.prodis', function ($q) { $q->where('jurusan_id', $this->selectedJurusanId);
            })->orWhereHas('cpmks.rps.matkul_rel.prodis', function ($q) { $q->where('jurusan_id', $this->selectedJurusanId);})
            ->orWhereHas('scpmks.cpmks.rps.matkul_rel.prodis', function ($q) { $q->where('jurusan_id', $this->selectedJurusanId);}); });
        }
        if (! empty($this->selectedFakultasId)) {
            $queryRef->where(function ($query) {$query->whereHas('rps.matkul_rel.prodis.jurusan_rel', function ($q) { $q->where('fakultas_id', $this->selectedFakultasId);
            })->orWhereHas('cpmks.rps.matkul_rel.prodis.jurusan_rel', function ($q) { $q->where('fakultas_id', $this->selectedFakultasId);})
            ->orWhereHas('scpmks.cpmks.rps.matkul_rel.prodis.jurusan_rel', function ($q) { $q->where('fakultas_id', $this->selectedFakultasId);}); });
        }
        if (! empty($this->selectedMatkulId)) {
            $queryRef->where(function ($query) {$query->whereHas('rps', function ($q) { $q->where('mk_id', $this->selectedMatkulId);
            })->orWhereHas('cpmks.rps', function ($q) { $q->where('mk_id', $this->selectedMatkulId);})
            ->orWhereHas('scpmks.cpmks.rps', function ($q) { $q->where('mk_id', $this->selectedMatkulId);}); });
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
