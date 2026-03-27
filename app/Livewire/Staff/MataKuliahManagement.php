<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;

use App\Livewire\Staff\MatkulManagement\WithMatkulFilters;
use App\Livewire\Staff\MatkulManagement\WithMatkulModal;
use App\Livewire\Staff\MatkulManagement\WithMatkulDelete;

use App\Models\ProgramStudi\Prodi;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Fakultas;

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

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshMatkulsList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted'  => ['except' => false]
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function refreshMatkulsList()
    {
        $this->resetPage();
    }

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

        $query = $this->inputMKSearch();

        if ($this->switchTable === 'tatap_muka') {
            $query->where('tipe_sks', 1);
        } elseif ($this->switchTable === 'praktikum') {
            $query->where('tipe_sks', 2);
        } elseif ($this->switchTable === 'praktek_lapangan') {
            $query->where('tipe_sks', 3);
        } elseif ($this->switchTable === 'simulasi') {
            $query->where('tipe_sks', 4);
        }

        $globalQuery = $this->inputMKSearch();

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
