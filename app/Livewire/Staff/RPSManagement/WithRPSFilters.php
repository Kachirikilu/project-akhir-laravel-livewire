<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

trait WithRPSFilters
{
    use WithPagination;

    public $search = '';

    public $filterRPS = '';

    public $searchBobotRPS = '';

    public function updatingSearchBobotRPS()
    {
        $this->resetPage();
    }

    public function resetInputBobotRPS()
    {
        $this->reset('searchBobotRPS');
        $this->resetPage();
    }

    public function inputRPSSearch()
    {
        $queryRPS = RPS::query()
            ->with(['mk_rel.prodis', 'mk_rel.prodis.dp_rel', 'mk_rel.prodis.dp_rel.fk_rel']);

        if ($this->switchTable === 'rps') {
            $search = $this->search;

            if (! empty($search)) {
                $queryRPS->searchRPS($search);
            }

            if (! empty($this->searchBobotRPS)) {
                $queryRPS->searchRPS($this->searchBobotRPS, true);
            }

            $this->sortFieldOrderRPS($queryRPS);

            if (! empty($this->selectedPrId)) {
                $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
            }
            // if (! empty($this->selectedDpId)) {
            //     $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('dp_id', $this->selectedDpId));
            // }
            // if (! empty($this->selectedFkId)) {
            //     $queryRPS->whereHas('mk_rel.prodis.dp_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
            // }
            if (! empty($this->selectedMKId)) {
                $queryRPS->where('rps.mk_id', $this->selectedMKId);
            }
            if (! empty($this->selectedDosenId)) {
                $queryRPS->whereHas('dosens', function ($q) {
                    $q->where('dosens.id', $this->selectedDosenId);
                });
            }

        }

        return $queryRPS;

    }

    public function buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgoYear)
    {
        if ($this->filterRPS === 'rps-akademik') {
            $queryRPS->where('akademik', 'like', '%'.$currentYear.'%');
        } elseif ($this->filterRPS === 'rps-rev-new') {
            $queryRPS->whereYear('revisi', $currentYear);
        } elseif ($this->filterRPS === 'rps-aktif') {
            $queryRPS->where('is_draf', false);
        } elseif ($this->filterRPS === 'rps-draf') {
            $queryRPS->where('is_draf', true);
        } elseif ($this->filterRPS === 'rps-older-5') {
            $queryRPS->whereRaw('RIGHT(akademik, 4) < ?', [$fiveYearsAgoYear]);
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
                DB::raw('(
                    COALESCE((
                        SELECT SUM(sub_cpmks.bobot)
                        FROM rps_pivot_cpmk
                        JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                        JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                        WHERE rps_pivot_cpmk.rps_id = rps.id
                    ), 0)
                    + CASE WHEN EXISTS(
                        SELECT 1
                        FROM rps_pivot_cpmk
                        JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                        JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                        WHERE rps_pivot_cpmk.rps_id = rps.id
                        AND UPPER(sub_cpmks.metode) = \'UTS\'
                    ) THEN 0 ELSE COALESCE(rps.bobot_uts, 0) END
                    + CASE WHEN EXISTS(
                        SELECT 1
                        FROM rps_pivot_cpmk
                        JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                        JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                        WHERE rps_pivot_cpmk.rps_id = rps.id
                        AND UPPER(sub_cpmks.metode) IN (\'UAS\', \'LAPORAN AKHIR\', \'HASIL PROYEK\', \'HASIL PROJEK\')
                    ) THEN 0 ELSE COALESCE(rps.bobot_uas, 0) END
                )'),
                $this->sortDirection
            ),

            'is_draf' => $queryRPS->orderBy('is_draf', $this->sortDirection),
            'revisi' => $queryRPS->orderBy('revisi', $this->sortDirection),
            'created_at' => $queryRPS->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryRPS->orderBy('updated_at', $this->sortDirection),

            default => $queryRPS->orderBy('id', $this->sortDirection),
        };
    }

    // private function applyRPSKodeSort($queryRPS)
    // {
    //     return $queryRPS->leftJoin('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
    //         ->leftJoin('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
    //         ->leftJoin('prodis', 'prodi_pivot_mk.pr_id', '=', 'prodis.id')
    //         ->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
    //         ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
    //         ->select('rps.*')
    //         ->groupBy('rps.id')
    //         ->orderBy(DB::raw("
    //             CONCAT(
    //                 MAX(CASE
    //                     WHEN mata_kuliahs.level_mk = 1 THEN UPPER(mata_kuliahs.kode_mk)
    //                     WHEN mata_kuliahs.level_mk = 2 THEN COALESCE(prodis.kode_pr, departemens.kode_dp, fakultas.kode_fk, 'UNI')
    //                     WHEN mata_kuliahs.level_mk = 3 THEN COALESCE(departemens.kode_dp, fakultas.kode_fk, 'UNI')
    //                     WHEN mata_kuliahs.level_mk = 4 THEN COALESCE(fakultas.kode_fk, 'UNI')
    //                     ELSE 'UNI'
    //                 END),
    //                 MAX(mata_kuliahs.digit_semester),
    //                 MAX(mata_kuliahs.digit_mk),
    //                 -- Menambahkan 2 digit terakhir tahun akademik (misal: 26) di akhir string sort
    //                 RIGHT(rps.akademik, 2)
    //             )
    //         "), $this->sortDirection);
    // }
    private function applyRPSKodeSort($queryRPS)
    {
        return $queryRPS->orderByRaw("
    (
        SELECT CONCAT(
            MIN(
                CASE 
                    WHEN mk.level_mk = 1 THEN COALESCE(p.kode_pr, j.kode_dp, f.kode_fk, 'UNI')
                    WHEN mk.level_mk = 2 THEN COALESCE(j.kode_dp, f.kode_fk, 'UNI')
                    WHEN mk.level_mk = 3 THEN COALESCE(f.kode_fk, 'UNI')
                    WHEN mk.level_mk = 4 THEN 'UNI'
                    ELSE mk.kode_mk
                END
            ),
            LPAD(mk.digit_semester, 2, '0'),
            LPAD(mk.digit_mk, 2, '0'),
            LPAD(RIGHT(rps.akademik, 2), 2, '0')
        )
        FROM mata_kuliahs mk
        LEFT JOIN prodi_pivot_mk ppm ON mk.id = ppm.mk_id
        LEFT JOIN prodis p ON ppm.pr_id = p.id
        LEFT JOIN departemens j ON p.dp_id = j.id
        LEFT JOIN fakultas f ON j.fk_id = f.id
        WHERE mk.id = rps.mk_id
    ) {$this->sortDirection}
");
    }
}
