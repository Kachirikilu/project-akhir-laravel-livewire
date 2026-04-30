<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Staff\MKManagement\WithMKDelete;
use App\Livewire\Staff\MKManagement\WithMKFilters;
use App\Livewire\Staff\MKManagement\WithMKModal;
use App\Models\Akademik\MataKuliah;
// use App\Models\ProgramStudi\Prodi;
// use App\Models\ProgramStudi\Departemen;
// use App\Models\ProgramStudi\Fakultas;

use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class MataKuliahManagement extends Component
{
    use WithFakultasSearchFilters;
    use WithDepartemenSearchFilters;
    use WithMKDelete;
    use WithMKFilters;
    use WithMKModal;
    use WithPagination;
    use WithProdiSearchFilters;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshMKsList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterMK' => ['except' => ''],
        'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterMK']);
        $this->resetPage();
    }

    public function refreshMKsList()
    {
        $this->resetPage();
    }

    public function buttonMKFilter($queryMK)
    {
        if ($this->filterMK === 'wajib') {
            $queryMK->where('is_wajib', true);
        } elseif ($this->filterMK === 'pilihan') {
            $queryMK->where('is_wajib', false);
        } elseif ($this->filterMK === 'universitas') {
            $queryMK->where('level_mk', 4);
        }
    }

    private function syncSortField($table, $sortField)
    {
        $map = [
            'tatap_muka' => 'sks_tm',
            'praktikum' => 'sks_pr',
            'praktek_lapangan' => 'sks_pl',
            'simulasi' => 'sks_sm',
        ];

        if (isset($map[$table]) && str_starts_with($sortField, 'sks_')) {
            $this->sortField = $map[$table];
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);

        $this->resetPage();
    }

    // public function render()
    // {
    //     // 1. Jalankan filter input (Prodi, Departemen, Fakultas)
    //     $this->inputPrFilter();
    //     $this->inputDpFilter();
    //     $this->inputFkFilter();

    //     try {
    //         // 2. Inisialisasi Base Query (Pencarian Utama)
    //         $baseQuery = $this->inputMKSearch()
    //             ->when($this->showDeleted, fn ($q) => $q->onlyTrashed());

    //         // 3. Ambil Data Mentah untuk Statistik (Gunakan clone agar tidak merusak baseQuery)
    //         $baseData = $baseQuery->clone()
    //             ->get([
    //                 'mata_kuliahs.id',
    //                 'mata_kuliahs.tipe_sks',
    //                 'mata_kuliahs.is_wajib',
    //                 'mata_kuliahs.level_mk',
    //             ])
    //             ->unique('id');

    //         // --- Perhitungan Statistik Statistik ---
    //         $totalSemuaMK = $baseData->count();
    //         $totalTatapMuka = $baseData->where('tipe_sks', 1)->count();
    //         $totalPraktikum = $baseData->where('tipe_sks', 2)->count();
    //         $totalPraktekLapangan = $baseData->where('tipe_sks', 3)->count();
    //         $totalSimulasi = $baseData->where('tipe_sks', 4)->count();

    //         // 4. Filter berdasarkan Tab (Switch Table)
    //         $mapTipe = [
    //             'tatap_muka' => 1,
    //             'praktikum' => 2,
    //             'praktek_lapangan' => 3,
    //             'simulasi' => 4,
    //         ];

    //         $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

    //         // Filter data untuk counter Opsi (Wajib, Pilihan, Uni) berdasarkan tab aktif
    //         $currentTabData = $currentTabTipe
    //             ? $baseData->where('tipe_sks', $currentTabTipe)
    //             : $baseData;

    //         $totalAllOpsi = $currentTabData->count();
    //         $totalWajib = $currentTabData->where('is_wajib', true)->count();
    //         $totalPilihan = $currentTabData->where('is_wajib', false)->count();
    //         $totalUni = $currentTabData->where('level_mk', 4)->count();

    //         // 5. Query Final untuk Tabel (Pagination)
    //         $queryMK = $baseQuery->clone();

    //         if ($currentTabTipe) {
    //             $queryMK->where('tipe_sks', $currentTabTipe);
    //         }

    //         // Terapkan filter tambahan dari button (jika ada)
    //         $this->buttonMKFilter($queryMK);

    //         return view('livewire.staff.mk-management', [
    //             'mks' => $queryMK->paginate($this->perPage),
    //             'totalAllOpsi' => $totalAllOpsi,
    //             'totalWajib' => $totalWajib,
    //             'totalPilihan' => $totalPilihan,
    //             'totalUni' => $totalUni,
    //             'totalSemuaMK' => $totalSemuaMK,
    //             'totalTatapMuka' => $totalTatapMuka,
    //             'totalPraktikum' => $totalPraktikum,
    //             'totalPraktekLapangan' => $totalPraktekLapangan,
    //             'totalSimulasi' => $totalSimulasi,
    //         ]);

    //     } catch (QueryException $e) {
    //         session()->flash('error', 'Terjadi kesalahan database: '.$e->getMessage());

    //         return view('livewire.staff.mk-management', [
    //             'mks' => MataKuliah::whereRaw('1 = 0')->paginate($this->perPage),
    //             'totalAllOpsi' => 0,
    //             'totalWajib' => 0,
    //             'totalPilihan' => 0,
    //             'totalUni' => 0,
    //             'totalSemuaMK' => 0,
    //             'totalTatapMuka' => 0,
    //             'totalPraktikum' => 0,
    //             'totalPraktekLapangan' => 0,
    //             'totalSimulasi' => 0,
    //         ]);
    //     }
    // }

    public function render()
    {
        // =========================
        // 1. FILTER INPUT
        // =========================
        $this->inputPrFilter();
        $this->inputDpFilter();
        $this->inputFkFilter();

        try {
            // =========================
            // 2. BASE QUERY (BERSIH - TANPA SORT)
            // =========================
            $baseQuery = $this->inputMKSearch()
                ->when($this->showDeleted, fn ($q) => $q->onlyTrashed());

            // =========================
            // 3. BASE DATA UNTUK STATISTIK (AMAN)
            // =========================
            $baseData = $baseQuery->clone()
                ->select(
                    'mata_kuliahs.id',
                    'mata_kuliahs.tipe_sks',
                    'mata_kuliahs.is_wajib',
                    'mata_kuliahs.level_mk'
                )
                ->distinct()
                ->get();

            // =========================
            // 4. STATISTIK GLOBAL
            // =========================
            $totalSemuaMK = $baseData->count();

            $totalTatapMuka = $baseData->where('tipe_sks', 1)->count();
            $totalPraktikum = $baseData->where('tipe_sks', 2)->count();
            $totalPraktekLapangan = $baseData->where('tipe_sks', 3)->count();
            $totalSimulasi = $baseData->where('tipe_sks', 4)->count();

            // =========================
            // 5. MAP TAB
            // =========================
            $mapTipe = [
                'tatap_muka' => 1,
                'praktikum' => 2,
                'praktek_lapangan' => 3,
                'simulasi' => 4,
            ];

            $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

            // =========================
            // 6. FILTER DATA UNTUK OPSI (Wajib, dll)
            // =========================
            $currentTabData = $currentTabTipe
                ? $baseData->where('tipe_sks', $currentTabTipe)
                : $baseData;

            $totalAllOpsi = $currentTabData->count();
            $totalWajib = $currentTabData->where('is_wajib', true)->count();
            $totalPilihan = $currentTabData->where('is_wajib', false)->count();
            $totalUni = $currentTabData->where('level_mk', 4)->count();

            // =========================
            // 7. QUERY TABEL (PAKAI SORT BERAT DI SINI SAJA)
            // =========================
            $queryMK = $baseQuery->clone();

            // filter tab
            if ($currentTabTipe) {
                $queryMK->where('mata_kuliahs.tipe_sks', $currentTabTipe);
            }

            // filter tombol tambahan
            $this->buttonMKFilter($queryMK);

            // APPLY SORT (JOIN BERAT DI SINI SAJA)
            $queryMK = $this->applyMKKodeSort($queryMK);

            // =========================
            // 8. RETURN VIEW
            // =========================
            return view('livewire.staff.mk-management', [
                'mks' => $queryMK->paginate($this->perPage),

                // Statistik opsi
                'totalAllOpsi' => $totalAllOpsi,
                'totalWajib' => $totalWajib,
                'totalPilihan' => $totalPilihan,
                'totalUni' => $totalUni,

                // Statistik global
                'totalSemuaMK' => $totalSemuaMK,
                'totalTatapMuka' => $totalTatapMuka,
                'totalPraktikum' => $totalPraktikum,
                'totalPraktekLapangan' => $totalPraktekLapangan,
                'totalSimulasi' => $totalSimulasi,
            ]);

        } catch (QueryException $e) {

            $this->toast(text: 'Terjadi kesalahan database: '.$e->getMessage(), variant: 'danger');

            return view('livewire.staff.mk-management', [
                'mks' => MataKuliah::whereRaw('1 = 0')->paginate($this->perPage),

                'totalAllOpsi' => 0,
                'totalWajib' => 0,
                'totalPilihan' => 0,
                'totalUni' => 0,

                'totalSemuaMK' => 0,
                'totalTatapMuka' => 0,
                'totalPraktikum' => 0,
                'totalPraktekLapangan' => 0,
                'totalSimulasi' => 0,
            ]);
        }
    }
}
