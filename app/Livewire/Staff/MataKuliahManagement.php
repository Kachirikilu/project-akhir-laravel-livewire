<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;

use App\Livewire\Staff\MatkulManagement\WithMatkulFilters;
use App\Livewire\Staff\MatkulManagement\WithMatkulModal;
use App\Livewire\Staff\MatkulManagement\WithMatkulDelete;

use App\Models\Akademik\MataKuliah;

// use App\Models\ProgramStudi\Prodi;
// use App\Models\ProgramStudi\Jurusan;
// use App\Models\ProgramStudi\Fakultas;

use Livewire\Component;
use Livewire\WithPagination;

class MataKuliahManagement extends Component
{
    use WithProdiSearchFilters;
    use WithJurusanSearchFilters;
    use WithFakultasSearchFilters;

    use WithMatkulFilters;
    use WithMatkulModal;
    use WithMatkulDelete;

    use WithPagination;

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
            'tatap_muka'       => 'sks_tm',
            'praktikum'        => 'sks_pr',
            'praktek_lapangan' => 'sks_pl',
            'simulasi'         => 'sks_sm',
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
        $this->inputProdiFilter();
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        $queryMK = $this->inputMKSearch();

        if ($this->switchTable === 'tatap_muka') {
            $queryMK->where('tipe_sks', 1);
        } elseif ($this->switchTable === 'praktikum') {
            $queryMK->where('tipe_sks', 2);
        } elseif ($this->switchTable === 'praktek_lapangan') {
            $queryMK->where('tipe_sks', 3);
        } elseif ($this->switchTable === 'simulasi') {
            $queryMK->where('tipe_sks', 4);
        }

        $baseData = $this->inputMKSearch()
            ->when($this->showDeleted, fn($q) => $q->onlyTrashed())
            ->get(['mata_kuliahs.id', 'mata_kuliahs.tipe_sks', 'mata_kuliahs.is_wajib', 'mata_kuliahs.tingkatan_mk',
                'mata_kuliahs.created_at', 'mata_kuliahs.updated_at'])
            ->unique('id');

        $totalSemuaMK         = $baseData->count();
        $totalTatapMuka       = $baseData->where('tipe_sks', 1)->count();
        $totalPraktikum       = $baseData->where('tipe_sks', 2)->count();
        $totalPraktekLapangan = $baseData->where('tipe_sks', 3)->count();
        $totalSimulasi        = $baseData->where('tipe_sks', 4)->count();

        $queryMK = $this->inputMKSearch();
        if ($this->showDeleted) $queryMK->onlyTrashed();

        $mapTipe = ['tatap_muka' => 1, 'praktikum' => 2, 'praktek_lapangan' => 3, 'simulasi' => 4];
        if (isset($mapTipe[$this->switchTable])) {
            $queryMK->where('tipe_sks', $mapTipe[$this->switchTable]);
        }

        $currentTabData = (isset($mapTipe[$this->switchTable])) 
            ? $baseData->where('tipe_sks', $mapTipe[$this->switchTable]) 
            : $baseData;

        $totalAllOpsi = $currentTabData->count();
        $totalWajib   = $currentTabData->where('is_wajib', true)->count();
        $totalPilihan = $currentTabData->where('is_wajib', false)->count();
        $totalUni     = $currentTabData->where('tingkatan_mk', 4)->count();

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
    }
}
