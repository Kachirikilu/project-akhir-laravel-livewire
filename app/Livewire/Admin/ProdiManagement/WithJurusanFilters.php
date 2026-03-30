<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Jurusan;
use Livewire\WithPagination;

trait WithJurusanFilters
{
    use WithPagination;

    public function inputJurusanSearch()
    {
        $queryJr = Jurusan::query()->with(['fakultas_rel', 'prodis']);
        $search = $this->search;

        if (! empty($search)) {
            $queryJr->searchJurusan($search)->get();
        }

        if (! empty($this->selectedFakultasId)) {
            $queryJr->where('jurusans.fakultas_id', $this->selectedFakultasId);
        }

        if (! empty($this->selectedJurusanId)) {
            $queryJr->where('jurusans.id', $this->selectedJurusanId);
        }

        $this->sortFieldOrderJurusan($queryJr);

        return $queryJr;
    }

    public function sortFieldOrderJurusan($queryJr)
    {
        $queryJr->select('jurusans.*')->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id');;

        return match ($this->sortField) {
            'kode'  => $queryJr->orderBy('kode_jr', $this->sortDirection)
                            ->orderBy('fakultas.kode_fk', $this->sortDirection),
            'jurusan'  => $queryJr->orderBy('nama_jurusan', $this->sortDirection),
            'fakultas' => $queryJr->orderBy('fakultas.nama_fakultas', $this->sortDirection),
            'created_at' => $queryJr->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryJr->orderBy('updated_at', $this->sortDirection),
            default    => $queryJr->orderBy('id', 'desc'),
        };
    }

}
