<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;


use App\Livewire\Staff\MatkulManagement\WithMatkulFilters;
use App\Livewire\Staff\MatkulManagement\WithMatkulModal;
use App\Livewire\Staff\MatkulManagement\WithMatkulDelete;


use App\Models\Prodi;
use App\Models\Jurusan;
use App\Models\Fakultas;

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

    // public $selectedMatkulName = '';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshMatkulsList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        // 'selectedMatkulName' => ['except' => ''],
        'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted'  => ['except' => false]

        // 'prodi_name' => ['except' => null],
        // 'jurusan_name' => ['except' => null],
        // 'fakultas_name' => ['except' => null]
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function refreshMatkulsList()
    {
        $this->resetPage();
    }

    // public function switchingTable($table)
    // {
    //     $this->switchTable = $table;
    //     // $this->syncSortField($table);

    //     // if ($table == 'jurusan' && $this->perPage > 50) {
    //     //     $this->perPage = 50;
    //     // }
    //     // if ($table == 'fakultas' && $this->perPage > 10) {
    //     //     $this->perPage = 10;
    //     // }

    //     $this->resetPage();
    // }

    public function buttonMKFilter($query)
    {
        if ($this->filter === 'wajib') {
            $query->where('is_wajib', true);
        } elseif ($this->filter === 'pilihan') {
            $query->where('is_wajib', false);
        } elseif ($this->filter === 'universitas') {
            $query->where('tingkatan_mk', 4);
        }
    }

    // public function render()
    // {
    //     $baseQuery = $this->inputMainSearch();
    //     $query = clone $baseQuery;
    //     $this->buttonMKFilter($query);

    //     if ($this->switchTable === 'matkuls') {
    //         $matkuls = $query->paginate($this->perPage);
    //     } elseif ($this->switchTable === 'tatap_muka') {

    //     } elseif ($this->switchTable === 'praktikum') {

    //     } elseif ($this->switchTable === 'praktek_lapangan') {

    //     } elseif ($this->switchTable === 'simulasi') {

    //     }

    //     return view('livewire.staff.matkul-management', [
    //         'matkuls' => $matkuls,
    //         'totalMatkuls' => (clone $baseQuery)->count(),
    //         'totalWajib' => (clone $baseQuery)->where('is_wajib', true)->count(),
    //         'totalPilihan' => (clone $baseQuery)->where('is_wajib', false)->count(),
    //         'totalUni' => (clone $baseQuery)->where('tingkatan_mk', 5)->count(),
    //     ]);
    // }

    private function syncSortField($table, $sortField)
    {
        if ($table == 'tatap_muka' && ($sortField == 'sks_pr' || $sortField == 'sks_pl' || $sortField == 'sks_sm')) {
            $this->sortField = 'sks_tm';
        } elseif ($table == 'praktikum' && ($sortField == 'sks_tm' || $sortField == 'sks_pl' || $sortField == 'sks_sm')) {
            $this->sortField = 'sks_pr';
        } elseif ($table == 'praktek_lapangan' && ($sortField == 'sks_tm' || $sortField == 'sks_pr' || $sortField == 'sks_sm')) {
            $this->sortField = 'sks_pl';
        } elseif ($table == 'praktek_lapangan' && ($sortField == 'sks_tm' || $sortField == 'sks_pr' || $sortField == 'sks_pl')) {
            $this->sortField = 'sks_sm';
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

        $query = $this->inputMainSearch();

        if ($this->switchTable === 'tatap_muka') {
            $query->where('tipe_sks', 1);
        } elseif ($this->switchTable === 'praktikum') {
            $query->where('tipe_sks', 2);
        } elseif ($this->switchTable === 'praktek_lapangan') {
            $query->where('tipe_sks', 3);
        } elseif ($this->switchTable === 'simulasi') {
            $query->where('tipe_sks', 4);
        }

        $globalQuery = $this->inputMainSearch();

        if ($this->showDeleted) {
            $query->onlyTrashed();
            $globalQuery->onlyTrashed();
        }

        $totalMatkuls = (clone $query)->count();
        $totalWajib = (clone $query)->where('is_wajib', true)->count();
        $totalPilihan = (clone $query)->where('is_wajib', false)->count();
        $totalUni = (clone $query)->where('tingkatan_mk', 4)->count();

        $totalTatapMuka = (clone $globalQuery)->where('tipe_sks', 1)->count();
        $totalPraktikum = (clone $globalQuery)->where('tipe_sks', 2)->count();
        $totalPraktekLapangan = (clone $globalQuery)->where('tipe_sks', 3)->count();
        $totalSimulasi = (clone $globalQuery)->where('tipe_sks', 4)->count();
        $totalSemuaMK = (clone $globalQuery)->count();

        $this->buttonMKFilter($query);

        return view('livewire.staff.matkul-management', [
            'matkuls' => $query->paginate($this->perPage),
            'totalMatkuls' => $totalMatkuls,
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
