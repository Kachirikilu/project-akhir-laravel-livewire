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
        $query = Referensi::query()->with(['rps.matkul_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = '%' . trim($this->search) . '%';


            //         $table->string('kode_ref', 10);
            // $table->string('judul');
            // $table->string('penulis');
            // $table->string('penerbit');
            // $table->year('tahun');
            // $table->string('link')->nullable();


        if (!empty($this->search)) {
            $query->where('referensis.kode_ref', 'like', $search)
                  ->orWhere('referensis.judul', 'like', $search)
                  ->orWhere('referensis.penulis', 'like', $search)
                  ->orWhere('referensis.penerbit', 'like', $search)
                  ->orWhere('referensis.tahun', 'like', $search)
                  ->orWhere('referensis.link', 'like', $search)
                  ->orWhere('referensis.id', 'like', $search);
        }

        $this->sortFieldOrderRef($query);

        if (! empty($this->selectedProdiId)) {
            $query->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        return $query;
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

    public function sortFieldOrderRef($query)
    {
        $query->select('referensis.*');

        if ($this->sortField === 'judul') {
            $query->orderBy('judul', $this->sortDirection);
        } elseif ($this->sortField === 'penulis') {
            $query->orderBy('penulis', $this->sortDirection);
        } elseif ($this->sortField === 'tahun') {
            $query->orderBy('tahun', $this->sortDirection);
        } elseif ($this->sortField === 'jenis') {
            $query->orderBy('jenis_referensi', $this->sortDirection);
        } else {
            $query->orderBy('referensis.id', $this->sortDirection);
        }

        return $query;
    }
}
