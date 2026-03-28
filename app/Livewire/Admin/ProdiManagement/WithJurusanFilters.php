<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Jurusan;
use Livewire\WithPagination;

trait WithJurusanFilters
{
    use WithPagination;

    public function inputJurusanSearch()
    {
        $query = Jurusan::query()->with(['fakultas_rel', 'prodis']);
        $search = $this->search;

        if (! empty($search)) {
            $query->searchJurusan($search)->get();
        }

        if (! empty($this->selectedFakultasId)) {
            $query->where('jurusans.fakultas_id', $this->selectedFakultasId);
        }

        if (! empty($this->selectedJurusanId)) {
            $query->where('jurusans.id', $this->selectedJurusanId);
        }

        $this->sortFieldOrderJurusan($query);

        return $query;
    }

    public function sortFieldOrderJurusan($query)
    {
        $query->select('jurusans.*');

        if ($this->sortField === 'jurusan') {
            $query->orderBy('jurusans.nama_jurusan', $this->sortDirection);

        } elseif ($this->sortField === 'fakultas') {
            $query->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->orderBy('fakultas.nama_fakultas', $this->sortDirection);

        } else {
            $query->orderBy('jurusans.id', $this->sortDirection);
        }

        return $query;
    }
}
