<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\RPS;
use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

trait WithRPSFilters
{
    use WithPagination;

    public $search = '';

    public $filterRPS = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inputRPSSearch()
    {
        $queryRPS = RPS::query()
            ->with(['mk_rel.prodis', 'mk_rel.prodis.jr_rel', 'mk_rel.prodis.jr_rel.fk_rel']);

        $search = $this->search;

        if (! empty($search)) {
            $queryRPS->searchRPS($search);
        }

        $this->sortFieldOrderRPS($queryRPS);

        if (! empty($this->selectedPrId)) {
            $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
        }
        if (! empty($this->selectedJrId)) {
            $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('jr_id', $this->selectedJrId));
        }
        if (! empty($this->selectedFkId)) {
            $queryRPS->whereHas('mk_rel.prodis.jr_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
        }
        if (! empty($this->selectedMKId)) {
            $queryRPS->where('rps.mk_id', $this->selectedMKId);
        }
        if (! empty($this->selectedDosenId)) {
            $queryRPS->whereHas('dosens', function ($q) {
                $q->where('dosens.id', $this->selectedDosenId);
            });
        }

        return $queryRPS;
    }

    public function buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgoYear)
    {
        if ($this->filterRPS === 'rps-akademik') {
            $queryRPS->where('akademik', 'like', '%'.$currentYear.'%');
        } elseif ($this->filterRPS === 'rps-ref-new') {
            $queryRPS->whereYear('revisi', $currentYear);
        } elseif ($this->filterRPS === 'rps-aktif') {
            $queryRPS->where('is_draf', false);
        } elseif ($this->filterRPS === 'rps-draf') {
            $queryRPS->where('is_draf', true);
        } elseif ($this->filterRPS === 'rps-5-years') {
            $queryRPS->whereRaw('LEFT(akademik, 4) >= ?', [$fiveYearsAgoYear]);
        } elseif ($this->filterRPS === 'rps-old') {
            $queryRPS->whereRaw('LEFT(akademik, 4) < ?', [$fiveYearsAgoYear]);
        }
    }

    public function filterByRPS($rps)
    {
        $this->filterRPS = $rps;
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterRPS', 'filterCPMK', 'filterSCPMK', 'filterCPL', 'filterRef']);
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

    // public function sortFieldOrderRPS($queryRPS)
    // {
    //     match ($this->switchTable) {
    //         'rps' => match ($this->sortField) {
    //             'mk' => $queryRPS->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
    //                             ->orderBy('mata_kuliahs.nama_mk', $this->sortDirection),

    //             'kode' => $this->applyRPSKodeSort($queryRPS),

    //             'akademik' => $queryRPS->orderBy('akademik', $this->sortDirection),
    //             'revisi' => $queryRPS->orderBy('revisi', $this->sortDirection),
    //             'is_draf'        => $queryRPS->orderBy('is_draf', $this->sortDirection),
    //             default          => $queryRPS->orderBy('rps.id', 'desc'),
    //         },

    //         'cpmk' => match ($this->sortField) {
    //             'kode'      => $queryRPS->orderBy('kode_cpmk', $this->sortDirection),
    //             'deskripsi' => $queryRPS->orderBy('deskripsi', $this->sortDirection),
    //             default     => $queryRPS->orderBy('id', 'desc'),
    //         },

    //         'scpmk' => match ($this->sortField) {
    //             'kode'      => $queryRPS->orderBy('kode_scpmk', $this->sortDirection),
    //             'deskripsi' => $queryRPS->orderBy('deskripsi', $this->sortDirection),
    //             'bobot'     => $queryRPS->orderBy('bobot', $this->sortDirection),
    //             default     => $queryRPS->orderBy('id', 'desc'),
    //         },

    //         'cpl' => match ($this->sortField) {
    //             'kode'      => $queryRPS->orderBy('kode_cpl', $this->sortDirection),
    //             'deskripsi' => $queryRPS->orderBy('deskripsi', $this->sortDirection),
    //             default     => $queryRPS->orderBy('id', 'desc'),
    //         },

    //         'ref' => match ($this->sortField) {
    //             'judul'   => $queryRPS->orderBy('judul', $this->sortDirection),
    //             'tahun'   => $queryRPS->orderBy('tahun', $this->sortDirection),
    //             'penulis' => $queryRPS->orderBy('penulis', $this->sortDirection),
    //             default   => $queryRPS->orderBy('id', 'desc'),
    //         },

    //         default => $queryRPS->orderBy('id', 'desc'),
    //     };

    //     return $queryRPS;
    // }

    public function sortFieldOrderRPS($queryRPS)
    {
        $queryRPS->select('rps.*');

        return match ($this->sortField) {
            'mk' => $queryRPS->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
                ->orderBy('mata_kuliahs.nama_mk', $this->sortDirection),

            'kode' => $this->applyRPSKodeSort($queryRPS),

            'akademik' => $queryRPS->orderBy('akademik', $this->sortDirection),

            'is_wajib' => $queryRPS->orderBy(
                MataKuliah::select('is_wajib')
                    ->whereColumn('mata_kuliahs.id', 'rps.mk_id')
                    ->limit(1),
                $this->sortDirection
            ),

            'sks' => $queryRPS->orderBy(
                MataKuliah::select('sks_kuliah')
                    ->whereColumn('mata_kuliahs.id', 'rps.mk_id')
                    ->limit(1),
                $this->sortDirection
            ),

            'count_cpmk' => $queryRPS->orderBy(
                DB::table('rps_pivot_cpmk')
                    ->selectRaw('count(*)')
                    ->whereColumn('rps_pivot_cpmk.rps_id', 'rps.id'),
                $this->sortDirection
            ),

            'count_scpmk' => $queryRPS->orderBy(
                DB::table('rps_pivot_cpmk')
                    ->join('cpmk_pivot_scpmk', 'rps_pivot_cpmk.cpmk_id', '=', 'cpmk_pivot_scpmk.cpmk_id')
                    ->selectRaw('count(cpmk_pivot_scpmk.scpmk_id)')
                    ->whereColumn('rps_pivot_cpmk.rps_id', 'rps.id'),
                $this->sortDirection
            ),

            'total_bobot' => $queryRPS->orderBy(
                DB::table('rps_pivot_cpmk')
                    ->join('cpmk_pivot_scpmk', 'rps_pivot_cpmk.cpmk_id', '=', 'cpmk_pivot_scpmk.cpmk_id')
                    ->join('sub_cpmks', 'cpmk_pivot_scpmk.scpmk_id', '=', 'sub_cpmks.id')
                    ->selectRaw('sum(sub_cpmks.bobot)')
                    ->whereColumn('rps_pivot_cpmk.rps_id', 'rps.id'),
                $this->sortDirection
            ),

            'is_draf' => $queryRPS->orderBy('is_draf', $this->sortDirection),
            'revisi' => $queryRPS->orderBy('revisi', $this->sortDirection),
            'created_at' => $queryRPS->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryRPS->orderBy('updated_at', $this->sortDirection),

            default => $queryRPS->orderBy('id', $this->sortDirection),
        };
    }

    private function applyRPSKodeSort($queryRPS)
    {
        return $queryRPS->leftJoin('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
            ->leftJoin('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
            ->leftJoin('prodis', 'prodi_pivot_mk.pr_id', '=', 'prodis.id')
            ->leftJoin('jurusans', 'prodis.jr_id', '=', 'jurusans.id')
            ->leftJoin('fakultas', 'jurusans.fk_id', '=', 'fakultas.id')
            ->select('rps.*')
            ->groupBy('rps.id')
            ->orderBy(DB::raw("
                CONCAT(
                    MAX(CASE 
                        WHEN mata_kuliahs.level_mk = 1 THEN UPPER(mata_kuliahs.kode_mk)
                        WHEN mata_kuliahs.level_mk = 2 THEN COALESCE(prodis.kode_pr, jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.level_mk = 3 THEN COALESCE(jurusans.kode_jr, fakultas.kode_fk, 'UNI')
                        WHEN mata_kuliahs.level_mk = 4 THEN COALESCE(fakultas.kode_fk, 'UNI')
                        ELSE 'UNI'
                    END),
                    MAX(mata_kuliahs.digit_semester),
                    MAX(mata_kuliahs.digit_mk),
                    -- Menambahkan 2 digit terakhir tahun akademik (misal: 26) di akhir string sort
                    RIGHT(rps.akademik, 2)
                )
            "), $this->sortDirection);
    }
}
