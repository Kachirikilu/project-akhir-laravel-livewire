<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\SubCPMK;
use Livewire\WithPagination;

trait WithSubCPMKFilters
{
    use WithPagination;

   public function inputSCPMKSearch()
    {
        $query = SubCPMK::query()->with(['cpmks.rps.matkul_rel', 'cpmks.rps.matkul_rel.prodis', 'cpmks.rps.matkul_rel.prodis.jurusan_rel', 'cpmks.rps.matkul_rel.prodis.jurusan_rel.fakultas_rel']);
        $search = '%' . trim($this->search) . '%';

        if (!empty($this->search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_scpmk', 'like', $search)
                  ->orWhere('indikator', 'like', $search);
            });
        }

        if (! empty($this->selectedProdiId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('cpmks.rps.matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        $this->sortFieldOrderSCPMK($query);
        return $query;
    }

    public function sortFieldOrderSCPMK($query)
    {
        $query->select('sub_cpmks.*');

        if ($this->sortField === 'kode') {
            $query->orderBy('kode_scpmk', $this->sortDirection);
        } elseif ($this->sortField === 'indikator') {
            $query->orderBy('indikator', $this->sortDirection);
        } elseif ($this->sortField === 'bobot') {
            $query->orderBy('bobot', $this->sortDirection);
        } else {
            $query->orderBy('sub_cpmks.id', $this->sortDirection);
        }

        return $query;
    }
}
