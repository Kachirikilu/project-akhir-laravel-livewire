<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\RPS;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

trait WithRPSFilters
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inputRPSSearch()
    {
        $search = $this->search;

        $query = RPS::query()
            ->with(['matkul_rel.prodis', 'matkul_rel.prodis.jurusan_rel', 'matkul_rel.prodis.jurusan_rel.fakultas_rel'])
            ->searchRPS($search);

        $this->sortFieldOrderRPS($query);
        
        if (! empty($this->selectedProdiId)) {
            $query->whereHas('matkul_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('matkul_rel.prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('matkul_rel.prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

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

    // public function sortFieldOrderRPS($query)
    // {
    //     match ($this->switchTable) {
    //         'rps' => match ($this->sortField) {
    //             'matkul' => $query->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
    //                             ->orderBy('mata_kuliahs.nama_matkul', $this->sortDirection),
                
    //             'kode' => $this->applyRPSKodeSort($query),
                
    //             'tahun_akademik' => $query->orderBy('tahun_akademik', $this->sortDirection),
    //             'tanggal_revisi' => $query->orderBy('tanggal_revisi', $this->sortDirection),
    //             'is_draf'        => $query->orderBy('is_draf', $this->sortDirection),
    //             default          => $query->orderBy('rps.id', 'desc'),
    //         },

    //         'cpmk' => match ($this->sortField) {
    //             'kode'      => $query->orderBy('kode_cpmk', $this->sortDirection),
    //             'deskripsi' => $query->orderBy('deskripsi', $this->sortDirection),
    //             default     => $query->orderBy('id', 'desc'),
    //         },

    //         'scpmk' => match ($this->sortField) {
    //             'kode'      => $query->orderBy('kode_scpmk', $this->sortDirection),
    //             'deskripsi' => $query->orderBy('deskripsi', $this->sortDirection),
    //             'bobot'     => $query->orderBy('bobot', $this->sortDirection),
    //             default     => $query->orderBy('id', 'desc'),
    //         },

    //         'cpl' => match ($this->sortField) {
    //             'kode'      => $query->orderBy('kode_cpl', $this->sortDirection),
    //             'deskripsi' => $query->orderBy('deskripsi', $this->sortDirection),
    //             default     => $query->orderBy('id', 'desc'),
    //         },

    //         'ref' => match ($this->sortField) {
    //             'judul'   => $query->orderBy('judul', $this->sortDirection),
    //             'tahun'   => $query->orderBy('tahun', $this->sortDirection),
    //             'penulis' => $query->orderBy('penulis', $this->sortDirection),
    //             default   => $query->orderBy('id', 'desc'),
    //         },

    //         default => $query->orderBy('id', 'desc'),
    //     };

    //     return $query;
    // }

    public function sortFieldOrderRPS($query)
    {
        $query->select('rps.*');

        return match ($this->sortField) {
            'matkul' => $query->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
                            ->orderBy('mata_kuliahs.nama_matkul', $this->sortDirection),
            
            'kode'   => $this->applyRPSKodeSort($query),
            
            'akademik' => $query->orderBy('tahun_akademik', $this->sortDirection),
            'revisi' => $query->orderBy('tanggal_revisi', $this->sortDirection),
            'is_draf'        => $query->orderBy('is_draf', $this->sortDirection),
            
            default => $query->orderBy('rps.id', $this->sortDirection),
        };
    }

    private function applyRPSKodeSort($query)
    {
        return $query->leftJoin('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
            ->leftJoin('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
            ->leftJoin('prodis', 'prodi_pivot_mk.prodi_id', '=', 'prodis.id')
            ->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
            ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
            ->select('rps.*')
            ->groupBy('rps.id')
            ->orderBy(DB::raw("
                CONCAT(
                    MAX(CASE 
                        WHEN mata_kuliahs.tingkatan_mk = 1 THEN UPPER(mata_kuliahs.kode_mk)
                        WHEN mata_kuliahs.tingkatan_mk = 2 THEN COALESCE(prodis.kode_pr, jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.tingkatan_mk = 3 THEN COALESCE(jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.tingkatan_mk = 4 THEN COALESCE(fakultas.kode_fk, 'UNI')
                        ELSE 'UNI'
                    END),
                    MAX(mata_kuliahs.digit_semester),
                    MAX(mata_kuliahs.digit_mk),
                    -- Menambahkan 2 digit terakhir tahun akademik (misal: 26) di akhir string sort
                    RIGHT(rps.tahun_akademik, 2)
                )
            "), $this->sortDirection);
    }
}
