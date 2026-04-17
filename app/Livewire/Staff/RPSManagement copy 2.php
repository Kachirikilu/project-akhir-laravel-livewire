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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
    //     $this->inputPrFilter();
    //     // $this->inputJrFilter();
    //     // $this->inputFkFilter();
    //     $this->inputMKFilter();
    //     $this->inputRPSFilter();
    //     $this->inputCPMKFilter();
    //     $this->inputSCPMKFilter();
    //     $this->inputCPLFilter();
    //     // $this->inputRefFilter();
    //     $this->inputDosenFilter();

    //     $queryRPS = $this->inputRPSSearch();
    //     $baseDataRPS = $this->inputRPSSearch()
    //         ->when($this->showDeleted, fn ($q) => $q->onlyTrashed())
    //         ->get(['rps.id', 'rps.mk_id', 'rps.akademik', 'rps.is_draf', 'rps.revisi'])
    //         ->unique('id');

    //     $queryCPMK = $this->inputCPMKSearch();
    //     $querySCPMK = $this->inputSCPMKSearch();
    //     $queryCPL = $this->inputCPLSearch();
    //     $queryRef = $this->inputRefSearch();
    //     // $queryDosen = $this->inputDosenSearch();

    //     $baseDataCPMK = (clone $queryCPMK)->get(['id', 'created_at']);
    //     $baseDataCPL = (clone $queryCPL)->get(['id', 'created_at']);
    //     $baseDataSCPMK = (clone $querySCPMK)->get(['id', 'created_at']);
    //     $baseDataCPL = (clone $queryCPL)->get(['id', 'created_at']);
    //     $baseDataRef = (clone $queryRef)->get(['id', 'created_at']);

    //     // $queryCPMK2 = clone $queryCPMK;
    //     // $querySCPMK2 = clone $querySCPMK;
    //     // $queryCPL2 = clone $queryCPL;
    //     // $queryRef2 = clone $queryRef;
    //     // $queryDosen2 = clone $queryDosen;

    //     // $queryJr = clone $queryJurusan;
    //     // $queryFk = clone $queryFakultas;

    //     if ($this->showDeleted) {
    //         $queryRPS->onlyTrashed();
    //         $queryCPMK->onlyTrashed();
    //         $querySCPMK->onlyTrashed();
    //         $queryCPL->onlyTrashed();
    //         $queryRef->onlyTrashed();

    //         // $queryCPMK2->onlyTrashed();
    //         // $querySCPMK2->onlyTrashed();
    //         // $queryCPL2->onlyTrashed();
    //         // $queryRef2  ->onlyTrashed();
    //         // $queryDosen->onlyTrashed();
    //     }

    //     $data = [
    //         'rps' => collect(),
    //         'cpmk' => collect(),
    //         'scpmk' => collect(),
    //         'cpl' => collect(),
    //         'ref' => collect(),
    //         // 'dosen' => collect()
    //     ];

    //     $now = Carbon::now();
    //     $sixMonthsAgo = Carbon::now()->subMonths(6);
    //     $currentYear = Carbon::now()->year;
    //     $threeYearsAgo = Carbon::now()->subYears(3);
    //     $fiveYearsAgo = Carbon::now()->subYears(5);
    //     $tenYearsAgo = Carbon::now()->subYears(10);

    //     $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
    //     $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //     $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //     $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //     $this->buttonRefFilter($queryRef, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);

    //     switch ($this->switchTable) {
    //         case 'rps':
    //             $data['rps'] = $queryRPS->paginate($this->perPage);
    //             break;
    //         case 'cpmk':
    //             $data['cpmk'] = $queryCPMK->paginate($this->perPage);
    //             break;
    //         case 'scpmk':
    //             $data['scpmk'] = $querySCPMK->paginate($this->perPage);
    //             break;
    //         case 'cpl':
    //             $data['cpl'] = $queryCPL->paginate($this->perPage);
    //             break;
    //         case 'ref':
    //             $data['ref'] = $queryRef->paginate($this->perPage);
    //             break;
    //             // case 'dosen':
    //             //     $data['dosen'] = $queryDosen->paginate($this->perPage);
    //             //     break;
    //     }

    //     $stats = [
    //         'rps-akademik' => 0, 'rps-ref-new' => 0, 'rps-aktif' => 0, 'rps-draf' => 0, 'rps-5-years' => 0,
    //         'cpmk-month' => 0, 'cpmk-6-months' => 0, 'cpmk-year' => 0, 'cpmk-old' => 0,
    //         'scpmk-month' => 0, 'scpmk-6-months' => 0, 'scpmk-year' => 0, 'scpmk-old' => 0,
    //         'cpl-month' => 0, 'cpl-6-months' => 0, 'cpl-year' => 0, 'cpl-5-years' => 0,
    //         'ref-year' => 0, 'ref-2-3-years' => 0, 'ref-4-5-years' => 0, 'ref-6-10-years' => 0, 'ref-old' => 0
    //     ];

    //     // switch ($this->switchTable) {
    //     //     case 'rps':
    //     $stats['rps-akademik'] = $baseDataRPS->filter(fn ($item) => str_contains($item->akademik, (string) $currentYear)
    //     )->count();

    //     $stats['rps-ref-new'] = $baseDataRPS->filter(fn($item) =>
    //             $item->revisi && Carbon::parse($item->revisi)->year == $currentYear
    //         )->count();

    //     $stats['rps-aktif'] = $baseDataRPS->where('is_draf', false)->count();
    //     $stats['rps-draf'] = $baseDataRPS->where('is_draf', true)->count();

    //     $stats['rps-5-years'] = $baseDataRPS->filter(function ($item) use ($fiveYearsAgo) {
    //         $startYear = (int) substr($item->akademik, 0, 4);

    //         return $startYear < $fiveYearsAgo->year;
    //     })->count();
    //     //     break;

    //     // case 'cpmk':
    //     // Contoh untuk CPMK (berlaku sama untuk SCPMK dan CPL)
    //     $stats['cpmk-month'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->isSameMonth($now) && $item->created_at->year == $currentYear
    //     )->count();

    //     $stats['cpmk-6-months'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->greaterThanOrEqualTo($sixMonthsAgo)
    //     )->count();

    //     $stats['cpmk-year'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->year == $currentYear
    //     )->count();

    //     $stats['cpmk-old'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->lessThan($fiveYearsAgo)
    //     )->count();
    //     // break;

    //     // case 'scpmk':
    //     $stats['scpmk-month'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->isSameMonth($now) && $item->created_at->year == $currentYear
    //     )->count();
    //     $stats['scpmk-6-months'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->greaterThanOrEqualTo($sixMonthsAgo)
    //     )->count();
    //     $stats['scpmk-year'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->year == $currentYear
    //     )->count();
    //     $stats['scpmk-old'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->lessThan($fiveYearsAgo)
    //     )->count();
    //     // break;

    //     // case 'cpl':
    //     $stats['cpl-month'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->isSameMonth($now) &&
    //         Carbon::parse($item->created_at)->year == $currentYear
    //     )->count();
    //     $stats['cpl-6-months'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->greaterThanOrEqualTo($sixMonthsAgo)
    //     )->count();
    //     $stats['cpl-year'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->year == $currentYear
    //     )->count();
    //     $stats['cpl-5-years'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->lessThan($fiveYearsAgo)
    //     )->count();
    //     //  break;
    //     // default:

    //     // Tahun ini
    //     $stats['ref-year'] = $baseDataRef->where('tahun', $currentYear)->count();
    //     $stats['ref-2-3-years'] = $baseDataRef->whereBetween('tahun', [$currentYear - 3, $currentYear - 2])->count();
    //     $stats['ref-4-5-years'] = $baseDataRef->whereBetween('tahun', [$currentYear - 5, $currentYear - 4])->count();
    //     $stats['ref-6-10-years'] = $baseDataRef->whereBetween('tahun', [$currentYear - 10, $currentYear - 6])->count();
    //     $stats['ref-old'] = $baseDataRef->where('tahun', '<', $currentYear - 10)->count();

    //     return view('livewire.staff.rps-management', array_merge($data, [
    //         'totalRPS' => $baseDataRPS->count(),
    //         'totalCPMK' => $baseDataCPMK->count(),
    //         'totalSCPMK' => $baseDataSCPMK->count(),
    //         'totalCPL' => $baseDataCPL->count(),
    //         'totalRef' => $baseDataRef->count(),

    //         'cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator,
    //         'scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator,
    //         'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
    //         'ref_rps_modal_paginator' => $this->ref_rps_modal_paginator,

    //         'stats' => $stats,
    //     ]));
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

            if ($this->showDeleted) {
                $queryRPS->onlyTrashed();
                $queryCPMK->onlyTrashed();
                $querySCPMK->onlyTrashed();
                $queryCPL->onlyTrashed();
                $queryRef->onlyTrashed();
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

            $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
            $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
            $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
            $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
            $this->buttonRefFilter($queryRef, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);


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
                    $data['rps'] = (clone $queryRPS)->paginate($this->perPage);
                    break;
                case 'cpmk':
                    $data['cpmk'] = (clone $queryCPMK)->paginate($this->perPage);
                    break;
                case 'scpmk':
                    $data['scpmk'] = (clone $querySCPMK)->paginate($this->perPage);
                    break;
                case 'cpl':
                    $data['cpl'] = (clone $queryCPL)->paginate($this->perPage);
                    break;
                case 'ref':
                    $data['ref'] = (clone $queryRef)->paginate($this->perPage);
                    break;
            }

            // =========================
            // STATS (FULL DATABASE SIDE)
            // =========================
            $stats = [

                // RPS
                'rps-akademik' => (clone $queryRPS)
                    ->where('akademik', 'like', "%$currentYear%")
                    ->count(),

                'rps-ref-new' => (clone $queryRPS)
                    ->whereYear('revisi', $currentYear)
                    ->count(),

                'rps-aktif' => (clone $queryRPS)
                    ->where('is_draf', false)
                    ->count(),

                'rps-draf' => (clone $queryRPS)
                    ->where('is_draf', true)
                    ->count(),

                'rps-5-years' => (clone $queryRPS)
                    ->whereRaw('CAST(SUBSTRING(akademik,1,4) AS UNSIGNED) < ?', [$fiveYearsAgo->year])
                    ->count(),

                // CPMK
                'cpmk-month' => (clone $queryCPMK)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                'cpmk-6-months' => (clone $queryCPMK)
                    ->where('created_at', '>=', $sixMonthsAgo)
                    ->count(),

                'cpmk-year' => (clone $queryCPMK)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                'cpmk-old' => (clone $queryCPMK)
                    ->where('created_at', '<', $fiveYearsAgo)
                    ->count(),

                // SCPMK
                'scpmk-month' => (clone $querySCPMK)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                'scpmk-6-months' => (clone $querySCPMK)
                    ->where('created_at', '>=', $sixMonthsAgo)
                    ->count(),

                'scpmk-year' => (clone $querySCPMK)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                'scpmk-old' => (clone $querySCPMK)
                    ->where('created_at', '<', $fiveYearsAgo)
                    ->count(),

                // CPL
                'cpl-month' => (clone $queryCPL)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                'cpl-6-months' => (clone $queryCPL)
                    ->where('created_at', '>=', $sixMonthsAgo)
                    ->count(),

                'cpl-year' => (clone $queryCPL)
                    ->whereYear('created_at', $currentYear)
                    ->count(),

                'cpl-5-years' => (clone $queryCPL)
                    ->where('created_at', '<', $fiveYearsAgo)
                    ->count(),

                // REFERENSI
                'ref-year' => (clone $queryRef)
                    ->where('tahun', $currentYear)
                    ->count(),

                'ref-2-3-years' => (clone $queryRef)
                    ->whereBetween('tahun', [$currentYear - 3, $currentYear - 2])
                    ->count(),

                'ref-4-5-years' => (clone $queryRef)
                    ->whereBetween('tahun', [$currentYear - 5, $currentYear - 4])
                    ->count(),

                'ref-6-10-years' => (clone $queryRef)
                    ->whereBetween('tahun', [$currentYear - 10, $currentYear - 6])
                    ->count(),

                'ref-old' => (clone $queryRef)
                    ->where('tahun', '<', $currentYear - 10)
                    ->count(),
            ];

            $pivotTables = [
                'rps_pivot_dosen',
                'rps_pivot_cpmk',
                'rps_pivot_cpl',
                'dosen_pivot_scpmk',
                'cpmk_pivot_scpmk',
                'cpmk_pivot_cpl',
                'cpmk_pivot_ref',
                'scpmk_pivot_ref',
                'prodi_pivot_cpl',
                'rps_pivot_ref',
            ];

            $totalPivot = collect($pivotTables)
                ->sum(fn ($table) => DB::table($table)->count());

            // =========================
            // TOTAL (NO GET)
            // =========================
            return view('livewire.staff.rps-management', array_merge($data, [

                'totalRPS' => (clone $queryRPS)->count(),
                'totalCPMK' => (clone $queryCPMK)->count(),
                'totalSCPMK' => (clone $querySCPMK)->count(),
                'totalCPL' => (clone $queryCPL)->count(),
                'totalRef' => (clone $queryRef)->count(),
                'totalPiv' => $totalPivot,

                'cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator,
                'scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator,
                'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
                'ref_rps_modal_paginator' => $this->ref_rps_modal_paginator,

                'stats' => $stats,
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
                    'rps-ref-new' => 0,
                    'rps-aktif' => 0,
                    'rps-draf' => 0,
                    'rps-5-years' => 0,

                    'cpmk-month' => 0,
                    'cpmk-6-months' => 0,
                    'cpmk-year' => 0,
                    'cpmk-old' => 0,

                    'scpmk-month' => 0,
                    'scpmk-6-months' => 0,
                    'scpmk-year' => 0,
                    'scpmk-old' => 0,

                    'cpl-month' => 0,
                    'cpl-6-months' => 0,
                    'cpl-year' => 0,
                    'cpl-5-years' => 0,

                    'ref-year' => 0,
                    'ref-2-3-years' => 0,
                    'ref-4-5-years' => 0,
                    'ref-6-10-years' => 0,
                    'ref-old' => 0,
                ],
            ]));
        }
    }

    // public function render()
    // {
    //     $this->inputPrFilter();
    //     $this->inputMKFilter();
    //     $this->inputRPSFilter();
    //     $this->inputCPMKFilter();
    //     $this->inputSCPMKFilter();
    //     $this->inputCPLFilter();
    //     $this->inputDosenFilter();

    //     try {

    //         $now = now();
    //         $sixMonthsAgo = now()->subMonths(6);
    //         $currentYear = now()->year;
    //         $threeYearsAgo = now()->subYears(3);
    //         $fiveYearsAgo = now()->subYears(5);
    //         $tenYearsAgo = now()->subYears(10);

    //         // =========================
    //         // BASE QUERY
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
    //         // ⚠️ APPLY BUTTON FILTER DI SINI (WAJIB DI AWAL)
    //         // =========================
    //         $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
    //         $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //         $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //         $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
    //         $this->buttonRefFilter($queryRef, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);

    //         // =========================
    //         // PAGINATION (SETELAH FILTER)
    //         // =========================
    //         $data = [
    //             'rps' => collect(),
    //             'cpmk' => collect(),
    //             'scpmk' => collect(),
    //             'cpl' => collect(),
    //             'ref' => collect(),
    //         ];

    //         $queryRPSClone = clone $queryRPS;
    //         $queryCPMKClone = clone $queryCPMK;
    //         $querySCPMKClone = clone $querySCPMK;
    //         $queryCPLClone = clone $queryCPL;
    //         $queryRefClone = clone $queryRef;

    //         switch ($this->switchTable) {
    //             case 'rps':
    //                 $data['rps'] = $queryRPSClone->paginate($this->perPage);
    //                 break;

    //             case 'cpmk':
    //                 $data['cpmk'] = $queryCPMKClone->paginate($this->perPage);
    //                 break;

    //             case 'scpmk':
    //                 $data['scpmk'] = $querySCPMKClone->paginate($this->perPage);
    //                 break;

    //             case 'cpl':
    //                 $data['cpl'] = $queryCPLClone->paginate($this->perPage);
    //                 break;

    //             case 'ref':
    //                 $data['ref'] = $queryRefClone->paginate($this->perPage);
    //                 break;
    //         }

    //         // =========================
    //         // STATS (PAKAI QUERY SUDAH DIFILTER)
    //         // =========================
    //         $stats = [
    //             'rps-akademik' => (clone $queryRPS)
    //                 ->where('akademik', 'like', "%$currentYear%")
    //                 ->count(),

    //             'rps-ref-new' => (clone $queryRPS)
    //                 ->whereYear('revisi', $currentYear)
    //                 ->count(),

    //             'rps-aktif' => (clone $queryRPS)
    //                 ->where('is_draf', false)
    //                 ->count(),

    //             'rps-draf' => (clone $queryRPS)
    //                 ->where('is_draf', true)
    //                 ->count(),

    //             'rps-5-years' => (clone $queryRPS)
    //                 ->whereRaw('CAST(SUBSTRING(akademik,1,4) AS UNSIGNED) < ?', [$fiveYearsAgo->year])
    //                 ->count(),
    //         ];


            
    //         $pivotTables = [
    //             'rps_pivot_dosen',
    //             'rps_pivot_cpmk',
    //             'rps_pivot_cpl',
    //             'dosen_pivot_scpmk',
    //             'cpmk_pivot_scpmk',
    //             'cpmk_pivot_cpl',
    //             'cpmk_pivot_ref',
    //             'scpmk_pivot_ref',
    //             'prodi_pivot_cpl',
    //             'rps_pivot_ref',
    //         ];

    //         $totalPivot = collect($pivotTables)
    //             ->sum(fn ($table) => DB::table($table)->count());

    //         // =========================
    //         // TOTAL (NO GET)
    //         // =========================
    //         return view('livewire.staff.rps-management', array_merge($data, [
    //             'totalRPS' => (clone $queryRPS)->count(),
    //             'totalCPMK' => (clone $queryCPMK)->count(),
    //             'totalSCPMK' => (clone $querySCPMK)->count(),
    //             'totalCPL' => (clone $queryCPL)->count(),
    //             'totalRef' => (clone $queryRef)->count(),
    //             'totalPiv' => $totalPivot,

    //             'stats' => $stats,
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

    //             'stats' => [
    //                 'rps-akademik' => 0,
    //                 'rps-ref-new' => 0,
    //                 'rps-aktif' => 0,
    //                 'rps-draf' => 0,
    //                 'rps-5-years' => 0,

    //                 'cpmk-month' => 0,
    //                 'cpmk-6-months' => 0,
    //                 'cpmk-year' => 0,
    //                 'cpmk-old' => 0,

    //                 'scpmk-month' => 0,
    //                 'scpmk-6-months' => 0,
    //                 'scpmk-year' => 0,
    //                 'scpmk-old' => 0,

    //                 'cpl-month' => 0,
    //                 'cpl-6-months' => 0,
    //                 'cpl-year' => 0,
    //                 'cpl-5-years' => 0,

    //                 'ref-year' => 0,
    //                 'ref-2-3-years' => 0,
    //                 'ref-4-5-years' => 0,
    //                 'ref-6-10-years' => 0,
    //                 'ref-old' => 0,
    //             ],
    //         ]));
    //     }
    // }
}
