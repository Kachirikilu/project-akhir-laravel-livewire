<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Fakultas;
use Livewire\WithPagination;

trait WithFakultasFilters
{
    use WithPagination;

    public function inputFakultasSearch()
    {
        $query = Fakultas::query()->with(['jurusans.prodis']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_fakultas', 'like', $searchTerm);
                if (is_numeric($this->search)) {
                    $q->orWhere('id', $this->search);
                }
                $q->orWhereHas('jurusans', function ($r) use ($searchTerm) {
                    $r->where('nama_jurusan', 'like', $searchTerm);
                });
                $q->orWhereHas('jurusans.prodis', function ($r) use ($searchTerm) {
                    $r->where('nama_prodi', 'like', $searchTerm);
                });

            });
        }

        if (! empty($this->selectedFakultasId)) {
            $query->where('id', $this->selectedFakultasId);
        }

        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('jurusans', function ($q) {
                $q->where('id', $this->selectedJurusanId);
            });
        }

        $this->sortFieldOrderFakultas($query);

        return $query;
    }

    public function sortFieldOrderFakultas($query)
    {
        $query->select('fakultas.*');

        if ($this->sortField === 'fakultas') {
            $query->orderBy('nama_fakultas', $this->sortDirection);
        } else {
            $query->orderBy('id', $this->sortDirection);
        }

        return $query;
    }
}
