<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Jurusan;
use Livewire\WithPagination;

trait WithJurusanFilters
{
    use WithPagination;

    // public $search = '';

    // public $filter = '';

    // public $sortField = 'prodi';

    // public $sortDirection = 'asc';

    // public function updatingSearch()
    // {
    //     $this->resetPage();
    // }

    // public function updatingSearchAngkatan()
    // {
    //     $this->resetPage();
    // }

    public function inputJurusanSearch()
    {
        $query = Jurusan::query()->with(['fakultas_rel']);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama_jurusan', 'like', "%{$this->search}%")
                    ->orWhere('id', $this->search)
                    ->orWhereHas('fakultas_rel', function ($sq) {
                        $sq->where('nama_fakultas', 'like', "%{$this->search}%")
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$this->search]);
                    });
            });
        }

        // Filter berdasarkan fakultas yang dipilih
        if (! empty($this->selectedFakultasId)) {
            $query->where('fakultas_id', $this->selectedFakultasId);
        }

        $this->sortFieldOrderJurusan($query);

        return $query;
    }

    // public function filterBy($strata)
    // {
    //     $this->filter = $strata;
    //     $this->resetPage();
    // }

    // public function resetInputFilter()
    // {
    //     $this->reset(['search', 'filter']);
    //     $this->resetFakultasFilter();
    //     $this->resetPage();
    // }

    // public function sortBy($field)
    // {
    //     if ($this->sortField === $field) {
    //         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    //     } else {
    //         $this->sortField = $field;
    //         $this->sortDirection = 'asc';
    //     }
    //     $this->resetPage();
    // }

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