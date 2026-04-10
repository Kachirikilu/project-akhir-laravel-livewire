<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Jurusan;
use Livewire\WithPagination;

trait WithJurusanFilters
{
    use WithPagination;

    public function inputJurusanSearch()
    {
        $queryJr = Jurusan::query()->with(['fk_rel', 'prodis']);
        $search = $this->search;

        if (! empty($search)) {
            $queryJr->searchJurusan($search)->get();
        }

        if (! empty($this->selectedFakultasId)) {
            $queryJr->where('jurusans.fk_id', $this->selectedFakultasId);
        }

        if (! empty($this->selectedJurusanId)) {
            $queryJr->where('jurusans.id', $this->selectedJurusanId);
        }

        $this->sortFieldOrderJurusan($queryJr);

        return $queryJr;
    }

    public function sortFieldOrderJurusan($queryJr)
    {
        $queryJr->select('jurusans.*')->join('fakultas', 'jurusans.fk_id', '=', 'fakultas.id');;

        return match ($this->sortField) {
            'kode'  => $queryJr->orderBy('kode_jr', $this->sortDirection)
                            ->orderBy('fakultas.kode_fk', $this->sortDirection),
            'jurusan'  => $queryJr->orderBy('nama_jr', $this->sortDirection),
            'fakultas' => $queryJr->orderBy('fakultas.nama_fk', $this->sortDirection),
            'created_at' => $queryJr->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryJr->orderBy('updated_at', $this->sortDirection),
            default    => $queryJr->orderBy('id', 'desc'),
        };
    }

}
