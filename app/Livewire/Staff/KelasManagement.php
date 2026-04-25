<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Staff\CPLManagement\WithCPLFilters;
use App\Livewire\Staff\CPLManagement\WithCPLModal;
use App\Livewire\Staff\CPMKManagement\WithCPMKFilters;
use App\Livewire\Staff\CPMKManagement\WithCPMKModal;
use App\Livewire\Staff\CPMKManagement\WithSubCPMKFilters;
use App\Livewire\Staff\CPMKManagement\WithSubCPMKModal;
use App\Livewire\Staff\ReferensiManagement\WithRefFilters;
use App\Livewire\Staff\ReferensiManagement\WithRefModal;
use App\Livewire\Staff\RPSManagement\WithDosenFilters;
use App\Livewire\Staff\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\RPSManagement\WithRPSModal;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use Livewire\Component;
use Livewire\WithPagination;

class RPSManagement extends Component
{
    use WithCPLFilters;
    use WithCPLModal;
    use WithCPLSearchFilters;
    use WithCPMKFilters;
    use WithCPMKModal;
    use WithCPMKSearchFilters;
    use WithDosenFilters;
    use WithDosenSearchFilters;
    use WithFakultasSearchFilters;
    use WithJurusanSearchFilters;
    use WithMKSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithReferensiSearchFilters;
    use WithRefFilters;
    use WithRefModal;
    use WithRPSFilters;
    use WithRPSModal;
    use WithRPSSearchFilters;
    use WithSubCPMKFilters;
    use WithSubCPMKModal;
    use WithSubCPMKSearchFilters;

    public $switchTable = 'rps';

    public $perPage = 8;

    public $search = '';

    public $showDeleted = false;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'switchTable' => ['except' => 'rps'],
        'filterRPS' => ['except' => ''],
        'filterCPMK' => ['except' => ''],
        'filterSCPMK' => ['except' => ''],
        'filterCPL' => ['except' => ''],
        'filterRef' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->cplNameSearch = [
            'rps' => '',
            'cpmk' => '',
        ];
        $this->cpl_id_array = [
            'rps' => [],
            'cpmk' => [],
        ];
        $this->cpl_items_array = [
            'rps' => [],
            'cpmk' => [],
        ];

        $this->refNameSearch = [
            'rps' => '',
            'cpmk' => '',
            'scpmk' => '',
        ];
        $this->ref_id_array = [
            'rps' => [],
            'cpmk' => [],
            'scpmk' => [],
        ];
        $this->ref_items_array = [
            'rps' => [],
            'cpmk' => [],
            'scpmk' => [],
        ];
    }

    public function loadingTable() {}

    public function loadingRPSList() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterRPS', 'filterCPMK', 'filterSCPMK', 'filterCPL', 'filterRef']);
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'mk', 5 => 'sks', 6 => 'is_wajib', 7 => 'count-cpmk', 8 => 'count-scpmk', 9 => 'total_bobot', 10 => 'is_draf', 11 => 'revisi', 12 => 'created_at', 13 => 'updated_at'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'count-scpmk', 5 => 'total_bobot', 6 => 'created_at', 7 => 'updated_at'],
            'scpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'materi', 5 => 'metodologi', 6 => 'indikator', 7 => 'metode', 8 => 'bobot', 9 => 'tugas', 10 => 'w_tugas', 11 => 'w_mandiri', 12 => 'created_at', 13 => 'updated_at'],
            'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'created_at', 5 => 'updated_at'],
            'ref' => [1 => 'id', 2 => 'kode', 3 => 'judul', 4 => 'penulis', 5 => 'penerbit', 6 => 'tahun', 7 => 'link', 8 => 'created_at', 9 => 'updated_at'],
        ];

        if (! isset($columns[$table])) {
            return;
        }

        $aliases = [
            'deskripsi' => ['mk', 'deskripsi', 'judul'],
            'materi' => ['materi', 'penulis'],
            'akademik' => ['akademik', 'bobot', 'total_bobot'],
            'is_draf' => ['is_draf', 'indikator'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        $normalizedField = $sortField;
        foreach ($aliases as $master => $related) {
            if (in_array($sortField, $related)) {
                $normalizedField = $master;
                break;
            }
        }

        $targetCols = $columns[$table];
        $currentPos = 1;

        foreach ($columns as $tableName => $cols) {
            foreach ($cols as $pos => $colName) {
                $isMatch = ($colName === $normalizedField);

                if (! $isMatch) {
                    foreach ($aliases as $master => $related) {
                        if (in_array($colName, $related) && in_array($normalizedField, $related)) {
                            $isMatch = true;
                            break;
                        }
                    }
                }

                if ($isMatch) {
                    $currentPos = $pos;
                    break 2;
                }
            }
        }

        $maxPosInTarget = max(array_keys($targetCols));
        $finalPos = min($currentPos, $maxPosInTarget);

        $this->sortField = $targetCols[$finalPos];
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        if ($table == 'cpl' && $this->perPage > 100) {
            $this->perPage = 100;
        }
        if ($table == 'ref' && $this->perPage > 150) {
            $this->perPage = 150;
        }
        if ($table == 'rps' && $this->perPage > 200) {
            $this->perPage = 200;
        }
        if ($table == 'cpmk' && $this->perPage > 300) {
            $this->perPage = 300;
        }
    }

    // public function render()
    // {
    //     // =========================
    //     // FILTER INPUT
    //     // =========================
    //     $this->inputPrFilter();
    //     $this->inputMKFilter();
    //     $this->inputRPSFilter();
    //     $this->inputCPMKFilter();
    //     $this->inputSCPMKFilter();
    //     $this->inputCPLFilter();
    //     $this->inputDosenFilter();

    //     try {

    //         // =========================
    //         // QUERY BASE
    //         // =========================
    //         $queryRPS = $this->inputRPSSearch();
    //         $queryCPMK = $this->inputCPMKSearch();
    //         $querySCPMK = $this->inputSCPMKSearch();
    //         $queryCPL = $this->inputCPLSearch();
    //         $queryRef = $this->inputRefSearch();

    //         if ($this->showDeleted) {
    //             $queryRPS->onlyTrashed();
    //             $queryCPMK->onlyTrashed();
    //             $querySCPMK->onlyTrashed();
    //             $queryCPL->onlyTrashed();
    //             $queryRef->onlyTrashed();
    //         }

    //         // =========================
    //         // TIME SETUP
    //         // =========================
    //         $now = now();
    //         $sixMonthsAgo = now()->subMonths(6);
    //         $currentYear = now()->year;
    //         $threeYearsAgo = now()->subYears(3);
    //         $fiveYearsAgo = now()->subYears(5);
    //         $tenYearsAgo = now()->subYears(10);

    //         // =========================
    //         // PAGINATION
    //         // =========================
    //         $data = [
    //             'rps' => collect(),
    //             'cpmk' => collect(),
    //             'scpmk' => collect(),
    //             'cpl' => collect(),
    //             'ref' => collect(),
    //         ];

    //         switch ($this->switchTable) {
    //             case 'rps':
    //                 $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
    //                 $data['rps'] = $queryRPS->paginate($this->perPage);

    //                 break;
    //             case 'cpmk':
    //                 $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //                 $data['cpmk'] = $queryCPMK->paginate($this->perPage);
    //                 break;
    //             case 'scpmk':
    //                 $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //                 $data['scpmk'] = $querySCPMK->paginate($this->perPage);
    //                 break;
    //             case 'cpl':
    //                 $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //                 $data['cpl'] = $queryCPL->paginate($this->perPage);
    //                 break;
    //             case 'ref':
    //                 $this->buttonRefFilter($queryRef, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);
    //                 $data['ref'] = $queryRef->paginate($this->perPage);
    //                 break;
    //         }

    //         // =========================
    //         // TOTAL (NO GET)
    //         // =========================
    //         return view('livewire.staff.rps-management', array_merge($data, [
    //             'totalRPS' => RPS::count(),
    //             'totalCPMK' => CPMK::count(),
    //             'totalSCPMK' => SubCPMK::count(),
    //             'totalCPL' => CPL::count(),
    //             'totalRef' => Referensi::count(),
    //             'totalPiv' => 0,
    //             'cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator,
    //             'scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator,
    //             'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
    //             'ref_rps_modal_paginator' => $this->ref_rps_modal_paginator,
    //         ]));
    //     } catch (QueryException $e) {

    //         $this->toast(text: 'Terjadi kesalahan database: '.$e->getMessage(), variant: 'danger');

    //         return view('livewire.staff.rps-management', array_merge([
    //             'rps' => RPS::whereRaw('1=0')->paginate($this->perPage),
    //             'cpmk' => CPMK::whereRaw('1=0')->paginate($this->perPage),
    //             'scpmk' => SubCPMK::whereRaw('1=0')->paginate($this->perPage),
    //             'cpl' => CPL::whereRaw('1=0')->paginate($this->perPage),
    //             'ref' => Referensi::whereRaw('1=0')->paginate($this->perPage),
    //         ], [
    //             'totalRPS' => 0,
    //             'totalCPMK' => 0,
    //             'totalSCPMK' => 0,
    //             'totalCPL' => 0,
    //             'totalRef' => 0,
    //             'totalPiv' => 0,
    //             'cpmk_rps_modal_paginator' => collect(),
    //             'scpmk_rps_modal_paginator' => collect(),
    //             'cpl_rps_modal_paginator' => collect(),
    //             'ref_rps_modal_paginator' => collect(),
    //         ]));
    //     }
    // }

    public function render()
    {
        // =========================
        // FILTER INPUT
        // =========================
        $this->inputPrFilter();
        $this->inputMKFilter();
        $this->inputRPSFilter();
        $this->inputCPMKFilter();
        $this->inputSCPMKFilter();
        $this->inputCPLFilter();
        $this->inputDosenFilter();

        try {

            // =========================
            // QUERY BASE
            // =========================
            $queryRPS = $this->inputRPSSearch();
            $queryCPMK = $this->inputCPMKSearch();
            $querySCPMK = $this->inputSCPMKSearch();
            $queryCPL = $this->inputCPLSearch();
            $queryRef = $this->inputRefSearch();

            $countRPS = RPS::query();
            $countCPMK = CPMK::query();
            $countSCPMK = SubCPMK::query();
            $countCPL = CPL::query();
            $countRef = Referensi::query();

            if ($this->showDeleted) {
                $queryRPS->onlyTrashed();
                $queryCPMK->onlyTrashed();
                $querySCPMK->onlyTrashed();
                $queryCPL->onlyTrashed();
                $queryRef->onlyTrashed();

                $countRPS->onlyTrashed();
                $countCPMK->onlyTrashed();
                $countSCPMK->onlyTrashed();
                $countCPL->onlyTrashed();
                $countRef->onlyTrashed();
            }

            // =========================
            // TIME SETUP
            // =========================
            $now = now();
            $sixMonthsAgo = now()->subMonths(6);
            $currentYear = now()->year;
            $threeYearsAgo = now()->subYears(3);
            $fiveYearsAgo = now()->subYears(5);
            $tenYearsAgo = now()->subYears(10);

            // =========================
            // PAGINATION
            // =========================
            $data = [
                'rps' => collect(),
                'cpmk' => collect(),
                'scpmk' => collect(),
                'cpl' => collect(),
                'ref' => collect(),
            ];

            switch ($this->switchTable) {
                case 'rps':
                    $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
                    $data['rps'] = $queryRPS->paginate($this->perPage);
                    break;
                case 'cpmk':
                    $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    $data['cpmk'] = $queryCPMK->paginate($this->perPage);
                    break;
                case 'scpmk':
                    $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    $data['scpmk'] = $querySCPMK->paginate($this->perPage);
                    break;
                case 'cpl':
                    $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    $data['cpl'] = $queryCPL->paginate($this->perPage);
                    break;
                case 'ref':
                    $this->buttonRefFilter($queryRef, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);
                    $data['ref'] = $queryRef->paginate($this->perPage);
                    break;
            }

            $stats = [

                // =========================
                // RPS
                // =========================
                'rps-akademik' => (clone $countRPS)
                    ->where('akademik', 'like', "%$currentYear%")
                    ->count(),

                'rps-rev-new' => (clone $countRPS)
                    ->whereYear('revisi', $currentYear)
                    ->count(),

                // 'rps-aktif'
                // 'rps-aktif' => (clone $countRPS)
                //     ->where('is_draf', false)
                //     ->count(),

                // 'rps-draf' => (clone $countRPS)
                //     ->where('is_draf', true)
                //     ->count(),

                // 'rps-older-5' => (clone $countRPS)
                //     ->whereRaw('CAST(SUBSTRING(akademik,1,4) AS UNSIGNED) < ?', [$fiveYearsAgo->year])
                //     ->count(),

                // =========================
                // CPMK
                // =========================
                'cpmk-month' => (clone $countCPMK)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                // 'cpmk-6-months' => (clone $countCPMK)
                //     ->where('created_at', '>=', $sixMonthsAgo)
                //     ->count(),

                // 'cpmk-year' => (clone $countCPMK)
                //     ->whereYear('created_at', $currentYear)
                //     ->count(),

                // 'cpmk-older-5' => (clone $countCPMK)
                //     ->where('created_at', '<', $fiveYearsAgo)
                //     ->count(),

                // =========================
                // SCPMK
                // =========================
                'scpmk-month' => (clone $countSCPMK)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                // 'scpmk-6-months' => (clone $countSCPMK)
                //     ->where('created_at', '>=', $sixMonthsAgo)
                //     ->count(),

                // 'scpmk-year' => (clone $countSCPMK)
                //     ->whereYear('created_at', $currentYear)
                //     ->count(),

                // 'scpmk-older-5' => (clone $countSCPMK)
                //     ->where('created_at', '<', $fiveYearsAgo)
                //     ->count(),

                // =========================
                // CPL
                // =========================
                'cpl-month' => (clone $countCPL)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                // 'cpl-6-months' => (clone $countCPL)
                //     ->where('created_at', '>=', $sixMonthsAgo)
                //     ->count(),

                // 'cpl-year' => (clone $countCPL)
                //     ->whereYear('created_at', $currentYear)
                //     ->count(),

                // 'cpl-older-5' => (clone $countCPL)
                //     ->where('created_at', '<', $fiveYearsAgo)
                //     ->count(),

                // =========================
                // REFERENSI
                // =========================
                'ref-year' => (clone $countRef)
                    ->where('tahun', $currentYear)
                    ->count(),

                // 'ref-2-3-years' => (clone $countRef)
                //     ->whereBetween('tahun', [$currentYear - 3, $currentYear - 2])
                //     ->count(),

                // 'ref-4-5-years' => (clone $countRef)
                //     ->whereBetween('tahun', [$currentYear - 5, $currentYear - 4])
                //     ->count(),

                // 'ref-6-10-years' => (clone $countRef)
                //     ->whereBetween('tahun', [$currentYear - 10, $currentYear - 6])
                //     ->count(),

                // 'ref-older-10' => (clone $countRef)
                //     ->where('tahun', '<', $currentYear - 10)
                //     ->count(),
            ];

            // =========================
            // TOTAL (NO GET)
            // =========================
            return view('livewire.staff.rps-management', array_merge($data, [
                'totalRPS' => RPS::count(),
                'totalCPMK' => CPMK::count(),
                'totalSCPMK' => SubCPMK::count(),
                'totalCPL' => CPL::count(),
                'totalRef' => Referensi::count(),
                'totalPiv' => 0,

                // 'cpmk_rps_modal_paginator' => collect(),
                // 'scpmk_rps_modal_paginator' => collect(),
                // 'cpl_rps_modal_paginator' => collect(),
                // 'ref_rps_modal_paginator' => collect(),
                'cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator,
                'scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator,
                'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
                'ref_rps_modal_paginator' => $this->ref_rps_modal_paginator,

                'stats' => $stats
            ]));
        } catch (QueryException $e) {

            $this->toast(text: 'Terjadi kesalahan database: '.$e->getMessage(), variant: 'danger');

            return view('livewire.staff.rps-management', array_merge([
                'rps' => RPS::whereRaw('1=0')->paginate($this->perPage),
                'cpmk' => CPMK::whereRaw('1=0')->paginate($this->perPage),
                'scpmk' => SubCPMK::whereRaw('1=0')->paginate($this->perPage),
                'cpl' => CPL::whereRaw('1=0')->paginate($this->perPage),
                'ref' => Referensi::whereRaw('1=0')->paginate($this->perPage),
            ], [

                'totalRPS' => 0,
                'totalCPMK' => 0,
                'totalSCPMK' => 0,
                'totalCPL' => 0,
                'totalRef' => 0,
                'totalPiv' => 0,

                'cpmk_rps_modal_paginator' => collect(),
                'scpmk_rps_modal_paginator' => collect(),
                'cpl_rps_modal_paginator' => collect(),
                'ref_rps_modal_paginator' => collect(),

                'stats' => [
                    'rps-akademik' => 0,
                    'rps-rev-new' => 0,
                    // 'rps-aktif' => 0,
                    // 'rps-draf' => 0,
                    // 'rps-older-5' => 0,

                    'cpmk-month' => 0,
                    // 'cpmk-6-months' => 0,
                    // 'cpmk-year' => 0,
                    // 'cpmk-older-5' => 0,

                    'scpmk-month' => 0,
                    // 'scpmk-6-months' => 0,
                    // 'scpmk-year' => 0,
                    // 'scpmk-older-5' => 0,

                    'cpl-month' => 0,
                    // 'cpl-6-months' => 0,
                    // 'cpl-year' => 0,
                    // 'cpl-older-5' => 0,

                    'ref-year' => 0,
                    // 'ref-2-3-years' => 0,
                    // 'ref-4-5-years' => 0,
                    // 'ref-6-10-years' => 0,
                    // 'ref-older-10' => 0,
                ],
            ]));
        }
    }
}
