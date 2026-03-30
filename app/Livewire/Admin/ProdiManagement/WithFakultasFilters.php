<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Fakultas;
use Livewire\WithPagination;

trait WithFakultasFilters
{
    use WithPagination;

    public function inputFakultasSearch()
    {
        $queryFk = Fakultas::query()->with(['jurusans', 'jurusans.prodis']);
        $search = $this->search;

        if (! empty($search)) {
            $queryFk->searchFakultas($search)->get();
        }

        if (! empty($this->selectedFakultasId)) {
            $queryFk->where('id', $this->selectedFakultasId);
        }

        if (! empty($this->selectedJurusanId)) {
            $queryFk->whereHas('jurusans', function ($q) {
                $q->where('id', $this->selectedJurusanId);
            });
        }

        $this->sortFieldOrderFakultas($queryFk);

        return $queryFk;
    }

    public function sortFieldOrderFakultas($queryFk)
    {
        $queryFk->select('fakultas.*');

        return match ($this->sortField) {
            'kode'  => $queryFk->orderBy('kode_fk', $this->sortDirection),
            'fakultas' => $queryFk->orderBy('nama_fakultas', $this->sortDirection),
            'created_at' => $queryFk->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryFk->orderBy('updated_at', $this->sortDirection),
            default    => $queryFk->orderBy('id', 'desc'),
        };
    }
}
