<?php

namespace App\Livewire\Staff;

use App\Models\Akademik\MataKuliah;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Staff\MatkulManagement\WithMatkulDelete;
use App\Livewire\Staff\MatkulManagement\WithMatkulFilters;
use App\Livewire\Staff\MatkulManagement\WithMatkulModal;
// use App\Models\ProgramStudi\Prodi;
// use App\Models\ProgramStudi\Jurusan;
// use App\Models\ProgramStudi\Fakultas;

use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class MataKuliahManagement extends Component
{
    use WithFakultasSearchFilters;
    use WithJurusanSearchFilters;
    use WithMatkulDelete;
    use WithMatkulFilters;
    use WithMatkulModal;
    use WithPagination;
    use WithProdiSearchFilters;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshMatkulsList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterMK' => ['except' => ''],
        'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterMK']);
        $this->resetPage();
    }

    public function refreshMatkulsList()
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
            $queryMK->where('tingkatan_mk', 4);
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

    public function render()
    {
        // 1. Jalankan filter input (Prodi, Jurusan, Fakultas)
        $this->inputProdiFilter();
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        try {
            // 2. Inisialisasi Base Query (Pencarian Utama)
            $baseQuery = $this->inputMKSearch()
                ->when($this->showDeleted, fn ($q) => $q->onlyTrashed());

            // 3. Ambil Data Mentah untuk Statistik (Gunakan clone agar tidak merusak baseQuery)
            $baseData = $baseQuery->clone()
                ->get([
                    'mata_kuliahs.id',
                    'mata_kuliahs.tipe_sks',
                    'mata_kuliahs.is_wajib',
                    'mata_kuliahs.tingkatan_mk',
                ])
                ->unique('id');

            // --- Perhitungan Statistik Statistik ---
            $totalSemuaMK = $baseData->count();
            $totalTatapMuka = $baseData->where('tipe_sks', 1)->count();
            $totalPraktikum = $baseData->where('tipe_sks', 2)->count();
            $totalPraktekLapangan = $baseData->where('tipe_sks', 3)->count();
            $totalSimulasi = $baseData->where('tipe_sks', 4)->count();

            // 4. Filter berdasarkan Tab (Switch Table)
            $mapTipe = [
                'tatap_muka' => 1,
                'praktikum' => 2,
                'praktek_lapangan' => 3,
                'simulasi' => 4,
            ];

            $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

            // Filter data untuk counter Opsi (Wajib, Pilihan, Uni) berdasarkan tab aktif
            $currentTabData = $currentTabTipe
                ? $baseData->where('tipe_sks', $currentTabTipe)
                : $baseData;

            $totalAllOpsi = $currentTabData->count();
            $totalWajib = $currentTabData->where('is_wajib', true)->count();
            $totalPilihan = $currentTabData->where('is_wajib', false)->count();
            $totalUni = $currentTabData->where('tingkatan_mk', 4)->count();

            // 5. Query Final untuk Tabel (Pagination)
            $queryMK = $baseQuery->clone();

            if ($currentTabTipe) {
                $queryMK->where('tipe_sks', $currentTabTipe);
            }

            // Terapkan filter tambahan dari button (jika ada)
            $this->buttonMKFilter($queryMK);

            return view('livewire.staff.matkul-management', [
                'matkuls' => $queryMK->paginate($this->perPage),
                'totalAllOpsi' => $totalAllOpsi,
                'totalWajib' => $totalWajib,
                'totalPilihan' => $totalPilihan,
                'totalUni' => $totalUni,
                'totalSemuaMK' => $totalSemuaMK,
                'totalTatapMuka' => $totalTatapMuka,
                'totalPraktikum' => $totalPraktikum,
                'totalPraktekLapangan' => $totalPraktekLapangan,
                'totalSimulasi' => $totalSimulasi,
            ]);

        } catch (QueryException $e) {
            session()->flash('error', 'Terjadi kesalahan database: '.$e->getMessage());

            return view('livewire.staff.matkul-management', [
                'matkuls' => MataKuliah::whereRaw('1 = 0')->paginate($this->perPage),
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
