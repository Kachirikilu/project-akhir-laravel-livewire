<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\CPL;
use Livewire\WithPagination;

trait WithCPLFilters
{
    use WithPagination;

  public function inputCPLSearch()
    {
        $query = CPL::query()->with(['cpmks.rps.matkul_rel', 'cpmks.rps.matkul_rel.prodis', 'cpmks.rps.matkul_rel.prodis.jurusan_rel', 'cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where('kode_cpl', 'like', $search)
                  ->orWhere('deskripsi', 'like', $search);
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
