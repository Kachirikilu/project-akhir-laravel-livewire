<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\RPS;
use Livewire\WithPagination;

trait WithRPSFilters
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public $sortField = 'id';

    public $sortDirection = 'asc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inputRPSSearch()
    {
        $query = RPS::query()->with(['mataKuliah.prodis']);
        $search = trim($this->search);
        $searchTerm = '%' . $search . '%';

        if (!empty($search)) {
            $query->where(function ($q) use ($search, $searchTerm) {
                $q->whereHas('mataKuliah', function ($mq) use ($searchTerm) {
                    $mq->where('nama_matkul', 'like', $searchTerm)
                    ->orWhere('kode_mk', 'like', $searchTerm);
                })
                ->orWhere('tahun_akademik', 'like', $searchTerm);

                $searchLower = strtolower($search);
                
                if (in_array($searchLower, ['draf', 'draft', 'konsep', 'aseli'])) {
                    $q->orWhere('is_draf', true);
                } 
                elseif (in_array($searchLower, ['aktif', 'active', 'publish', 'published', 'siap'])) {
                    $q->orWhere('is_draf', false);
                }
            });
        }

        $this->sortFieldOrderRPS($query);
        return $query;
    }

    public function filterBy($rps)
    {
        $this->filter = $rps;
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filter']);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function sortFieldOrderRPS($query)
    {
        match ($this->switchTable) {
            'rps' => match ($this->sortField) {
                'matkul' => $query->join('mata_kuliahs', 'rps.matkul_id', '=', 'mata_kuliahs.id')
                                  ->orderBy('mata_kuliahs.nama_matkul', $this->sortDirection),
                'tahun'  => $query->orderBy('tahun_akademik', $this->sortDirection),
                default  => $query->orderBy('id', 'desc'),
            },
            'cpmk', 'scpmk', 'cpl' => match ($this->sortField) {
                'kode' => $query->orderBy($this->switchTable === 'cpl' ? 'kode_cpl' : 'kode_'.$this->switchTable, $this->sortDirection),
                default => $query->orderBy('id', 'desc'),
            },
            'ref' => match ($this->sortField) {
                'judul' => $query->orderBy('judul', $this->sortDirection),
                'tahun' => $query->orderBy('tahun', $this->sortDirection),
                default => $query->orderBy('id', 'desc'),
            },
            default => $query->orderBy('id', 'desc'),
        };

        return $query;
    }
}
