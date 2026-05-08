<?php

namespace App\Livewire\Staff;

use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
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
use App\Models\Auth\Dosen;
use App\Models\Auth\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class RPSManagement extends Component
{
    use WithCPLFilters;
    use WithCPLModal;
    use WithCPLSearchFilters;
    use WithCPMKFilters;
    use WithCPMKModal;
    use WithCPMKSearchFilters;
    use WithDepartemenSearchFilters;
    use WithDosenFilters;
    use WithDosenSearchFilters;
    use WithFakultasSearchFilters;
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
    use WithUserDelete;
    use WithUserFilters;
    use WithUserModal;

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
        'filterDosen' => ['except' => ''],
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

    public function updatingSearch()
    {
        $this->resetPage();
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

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'kode_mk', 4 => 'akademik', 5 => 'mk', 6 => 'sks', 7 => 'sks_text', 8 => 'is_wajib', 9 => 'count-cpmk', 10 => 'count-scpmk', 11 => 'total_bobot', 12 => 'is_draf', 13 => 'revisi', 14 => 'created_at', 15 => 'updated_at'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'count-scpmk', 5 => 'total_bobot', 6 => 'created_at', 7 => 'updated_at'],
            'scpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'materi', 5 => 'metodologi', 6 => 'indikator', 7 => 'metode', 8 => 'bobot', 9 => 'tugas', 10 => 'w_tugas', 11 => 'w_mandiri', 12 => 'created_at', 13 => 'updated_at'],
            'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'created_at', 5 => 'updated_at'],
            'ref' => [1 => 'id', 2 => 'kode', 3 => 'judul', 4 => 'penulis', 5 => 'penerbit', 6 => 'tahun', 7 => 'link', 8 => 'created_at', 9 => 'updated_at'],
            'dosen' => [1 => 'id', 2 => 'name', 3 => 'identity1', 4 => 'identity2', 5 => 'identity3', 6 => 'role', 7 => 'prodi', 8 => 'status', 9 => 'created_at', 10 => 'updated_at'],
        ];

        if (! isset($columns[$table])) {
            return;
        }

        $aliases = [
            'kode' => ['kode', 'name'],
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
        if ($table == 'dosen' && $this->perPage > 200) {
            $this->perPage = 200;
        }
        if ($table == 'cpmk' && $this->perPage > 300) {
            $this->perPage = 300;
        }
        if ($table == 'scpmk' && $this->perPage > 500) {
            $this->perPage = 500;
        }
    }

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
        $this->inputFkFilter();

        // $this->updatedMKNameSearch($this->mkNameSearch);
        // $this->updatedPrNameSearch($this->prNameSearch);

        try {

            // =========================
            // QUERY BASE
            // =========================
            $queryRPS = $this->inputRPSSearch();
            $queryCPMK = $this->inputCPMKSearch();
            $querySCPMK = $this->inputSCPMKSearch();
            $queryCPL = $this->inputCPLSearch();
            $queryRef = $this->inputRefSearch();
            $queryUser = $this->inputUserSearch();

            $countRPS = RPS::query();
            $countCPMK = CPMK::query();
            $countSCPMK = SubCPMK::query();
            $countCPL = CPL::query();
            $countRef = Referensi::query();
            $countDosen = User::whereHas('dosen');

            if ($this->showDeleted) {
                $queryRPS->onlyTrashed();
                $queryCPMK->onlyTrashed();
                $querySCPMK->onlyTrashed();
                $queryCPL->onlyTrashed();
                $queryRef->onlyTrashed();
                $queryUser->onlyTrashed();

                $countRPS->onlyTrashed();
                $countCPMK->onlyTrashed();
                $countSCPMK->onlyTrashed();
                $countCPL->onlyTrashed();
                $countRef->onlyTrashed();
                $countDosen->onlyTrashed();
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

            $users = collect();

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
                case 'dosen':
                    $this->buttonUserFilter($queryUser);
                    $users = $queryUser->paginate($this->perPage);
                    break;
            }

            if (Auth::user()->dosen) {
                $totalRPSSaya = (clone $countRPS)->whereHas('dosens', function ($q) {
                    $q->where('dosens.id', Auth::user()->dosen->id);
                })->count();
            }

            $stats = [
                'rps-prodi' => '🏦',
                'rps-akademik' => '📘',
                'rps-rev-new' => '✨',
                'rps-aktif' => '✅',
                'rps-draf' => '📝',
                'rps-older-5' => '⏳',

                'cpmk-month' => '🧩',
                'cpmk-6-months' => '⏱️',
                'cpmk-year' => '📆',
                'cpmk-older-5' => '⏳',

                'scpmk-month' => '🔗',
                'scpmk-6-months' => '⏱️',
                'scpmk-year' => '📆',
                'scpmk-older-5' => '⏳',

                'cpl-month' => '🎯',
                'cpl-6-months' => '⏱️',
                'cpl-year' => '📆',
                'cpl-older-5' => '⏳',

                'ref-year' => '📚',
                'ref-2-3-years' => '2️⃣',
                'ref-4-5-years' => '4️⃣',
                'ref-6-10-years' => '🔟',
                'ref-older-10' => '⏳',

                'dosen-rps' => '✅',
                'dosen-non-rps' => '❌',
                'dosen-aktif' => '🟢',
                'dosen-non-aktif' => '🔴',
            ];

            // =========================
            // SWITCH STATS (TIDAK OVERWRITE)
            // =========================
            switch ($this->switchTable) {
                case 'rps':
                    $stats['rps-prodi'] = (clone $countRPS)
                        ->whereHas('mk_rel.prodis', function ($q) {
                            $q->where('prodis.id', Auth::user()->pr_id);
                        })->count();

                    $stats['rps-akademik'] = (clone $countRPS)
                        ->where('akademik', 'like', "%$currentYear%")
                        ->count();

                    $stats['rps-rev-new'] = (clone $countRPS)
                        ->whereYear('revisi', $currentYear)
                        ->count();

                    $stats['rps-aktif'] = (clone $countRPS)
                        ->where('is_draf', false)
                        ->count();

                    $stats['rps-draf'] = (clone $countRPS)
                        ->where('is_draf', true)
                        ->count();

                    $stats['rps-older-5'] = (clone $countRPS)
                        ->whereRaw('CAST(SUBSTRING(akademik,1,4) AS UNSIGNED) < ?', [$fiveYearsAgo->year])
                        ->count();
                    break;

                case 'cpmk':
                    $stats['cpmk-month'] = (clone $countCPMK)
                        ->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $currentYear)
                        ->count();

                    $stats['cpmk-6-months'] = (clone $countCPMK)
                        ->where('created_at', '>=', $sixMonthsAgo)
                        ->count();

                    $stats['cpmk-year'] = (clone $countCPMK)
                        ->whereYear('created_at', $currentYear)
                        ->count();

                    $stats['cpmk-older-5'] = (clone $countCPMK)
                        ->where('created_at', '<', $fiveYearsAgo)
                        ->count();
                    break;

                case 'scpmk':
                    $stats['scpmk-month'] = (clone $countSCPMK)
                        ->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $currentYear)
                        ->count();

                    $stats['scpmk-6-months'] = (clone $countSCPMK)
                        ->where('created_at', '>=', $sixMonthsAgo)
                        ->count();

                    $stats['scpmk-year'] = (clone $countSCPMK)
                        ->whereYear('created_at', $currentYear)
                        ->count();

                    $stats['scpmk-older-5'] = (clone $countSCPMK)
                        ->where('created_at', '<', $fiveYearsAgo)
                        ->count();
                    break;

                case 'cpl':
                    $stats['cpl-month'] = (clone $countCPL)
                        ->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $currentYear)
                        ->count();

                    $stats['cpl-6-months'] = (clone $countCPL)
                        ->where('created_at', '>=', $sixMonthsAgo)
                        ->count();

                    $stats['cpl-year'] = (clone $countCPL)
                        ->whereYear('created_at', $currentYear)
                        ->count();

                    $stats['cpl-older-5'] = (clone $countCPL)
                        ->where('created_at', '<', $fiveYearsAgo)
                        ->count();
                    break;

                case 'ref':
                    $stats['ref-year'] = (clone $countRef)
                        ->where('tahun', $currentYear)
                        ->count();

                    $stats['ref-2-3-years'] = (clone $countRef)
                        ->whereBetween('tahun', [$currentYear - 3, $currentYear - 2])
                        ->count();

                    $stats['ref-4-5-years'] = (clone $countRef)
                        ->whereBetween('tahun', [$currentYear - 5, $currentYear - 4])
                        ->count();

                    $stats['ref-6-10-years'] = (clone $countRef)
                        ->whereBetween('tahun', [$currentYear - 10, $currentYear - 6])
                        ->count();

                    $stats['ref-older-10'] = (clone $countRef)
                        ->where('tahun', '<', $currentYear - 10)
                        ->count();
                    break;

                case 'dosen':
                    $stats['dosen-rps'] = (clone $countDosen)
                        ->whereHas('dosen.rps')
                        ->count();

                    $stats['dosen-non-rps'] = (clone $countDosen)
                        ->whereDoesntHave('dosen.rps')
                        ->count();

                    $stats['dosen-aktif'] = (clone $countDosen)
                        ->whereHas('dosen', function ($q) {
                            $q->where('status', 'aktif');
                        })
                        ->count();

                    $stats['dosen-non-aktif'] = (clone $countDosen)
                        ->whereHas('dosen', function ($q) {
                            $q->where('status', '!=', 'aktif');
                        })
                        ->count();
                    break;
            }

            // =========================
            // TOTAL (NO GET)
            // =========================
            return view('livewire.staff.rps-management', array_merge($data, [
                'users' => $users,
                'totalRPSSaya' => $totalRPSSaya ?? 0,
                'totalRPS' => RPS::count(),
                'totalCPMK' => CPMK::count(),
                'totalSCPMK' => SubCPMK::count(),
                'totalCPL' => CPL::count(),
                'totalRef' => Referensi::count(),
                'totalDosen' => Dosen::count(),

                'cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator,
                'scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator,
                'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
                'ref_rps_modal_paginator' => $this->ref_rps_modal_paginator,
                'dosen_rps_modal_paginator' => $this->dosen_rps_modal_paginator,

                'stats' => $stats,
            ]));
        } catch (QueryException $e) {

            $this->toast(text: 'Terjadi kesalahan database: '.$e->getMessage(), variant: 'danger');

            return view('livewire.staff.rps-management', [
                'rps' => RPS::whereRaw('1=0')->paginate($this->perPage),
                'cpmk' => CPMK::whereRaw('1=0')->paginate($this->perPage),
                'scpmk' => SubCPMK::whereRaw('1=0')->paginate($this->perPage),
                'cpl' => CPL::whereRaw('1=0')->paginate($this->perPage),
                'ref' => Referensi::whereRaw('1=0')->paginate($this->perPage),
            ], [
                'users' => User::whereRaw('1=0')->whereHas('dosen')->paginate($this->perPage),
                'totalRPSSaya' => '-',
                'totalRPS' => '-',
                'totalCPMK' => '-',
                'totalSCPMK' => '-',
                'totalCPL' => '-',
                'totalRef' => '-',
                'totalDosen' => '-',

                'cpmk_rps_modal_paginator' => collect(),
                'scpmk_rps_modal_paginator' => collect(),
                'cpl_rps_modal_paginator' => collect(),
                'ref_rps_modal_paginator' => collect(),
                'dosen_rps_modal_paginator' => collect(),

                'stats' => [
                    'rps-prodi' => '-',
                    'rps-akademik' => '-',
                    'rps-rev-new' => '-',
                    'rps-aktif' => '-',
                    'rps-draf' => '-',
                    'rps-older-5' => '-',

                    'cpmk-month' => '-',
                    'cpmk-6-months' => '-',
                    'cpmk-year' => '-',
                    'cpmk-older-5' => '-',

                    'scpmk-month' => '-',
                    'scpmk-6-months' => '-',
                    'scpmk-year' => '-',
                    'scpmk-older-5' => '-',

                    'cpl-month' => '-',
                    'cpl-6-months' => '-',
                    'cpl-year' => '-',
                    'cpl-older-5' => '-',

                    'ref-year' => '-',
                    'ref-2-3-years' => '-',
                    'ref-4-5-years' => '-',
                    'ref-6-10-years' => '-',
                    'ref-older-10' => '-',

                    'dosen-rps' => '-',
                    'dosen-non-rps' => '-',
                    'dosen-aktif' => '-',
                    'dosen-non-aktif' => '-',
                ],
            ]);
        }
    }
}
