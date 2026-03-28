<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\Referensi;
use Livewire\WithPagination;

trait WithReferensiFilters
{
    use WithPagination;

  public function inputRefSearch()
    {
        $query = Referensi::query()->with(['rps.matkul_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where('judul', 'like', $search)
                  ->orWhere('penulis', 'like', $search)
                  ->orWhere('tahun', 'like', $search);
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
