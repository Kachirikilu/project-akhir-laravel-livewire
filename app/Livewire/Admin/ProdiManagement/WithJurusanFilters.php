<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Jurusan;
use Livewire\WithPagination;

trait WithJurusanFilters
{
    use WithPagination;

    public function inputJurusanSearch()
    {
        $query = Jurusan::query()->with(['fakultas_rel', 'prodis']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('jurusans.nama_jurusan', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT('Jurusan ', jurusans.nama_jurusan) LIKE ?", [$searchTerm]);
                if (is_numeric($this->search)) {
                    $q->orWhere('jurusans.id', $this->search);
                }
                $q->orWhereHas('fakultas_rel', function ($r) use ($searchTerm) {
                    $r->where('nama_fakultas', 'like', $searchTerm);
                });
                $q->orWhereHas('prodis', function ($r) use ($searchTerm) {
                    $r->where('nama_prodi', 'like', $searchTerm);
                });

            });
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
