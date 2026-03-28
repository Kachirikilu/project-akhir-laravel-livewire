<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\CPMK;
use Livewire\WithPagination;

trait WithCPMKFilters
{
    use WithPagination;

    public function inputCPMKSearch()
    {
        $query = CPMK::query()
        ->with(['rps.matkul_rel.prodis.jurusan_rel', 'rps.matkul_rel.prodis', 'rps.matkul_rel']);
        $search = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_cpmk', 'like', $search)
                  ->orWhere('deskripsi', 'like', $search);
            });
        }

        if (! empty($this->selectedProdiId)) {
            $query->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        $this->sortFieldOrderCPMK($query);
        return $query;
    }

    public function sortFieldOrderCPMK($query)
    {
        $query->select('cpmks.*');

        if ($this->sortField === 'kode') {
            $query->orderBy('kode_cpmk', $this->sortDirection);
        } elseif ($this->sortField === 'deskripsi') {
            $query->orderBy('deskripsi', $this->sortDirection);
        } else {
            $query->orderBy('cpmks.id', $this->sortDirection);
        }

        return $query;
    }
}
