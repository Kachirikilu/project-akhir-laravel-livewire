<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;

use App\Livewire\Staff\MKManagement\WithMKFilters;
use App\Livewire\Staff\MKManagement\WithMKModal;
use App\Livewire\Staff\MKManagement\WithMKDelete;

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

    use WithMKFilters;
    use WithMKModal;
    use WithMKDelete;

    use WithPagination;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';

    protected $paginationTheme = 'tailwind';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshMKsList',
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

    public function refreshMKsList()
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
            $query->where('level_mk', 4);
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

        $baseQuery = $this->inputMKSearch();

        if ($this->showDeleted) {
            $query->onlyTrashed();
            $baseQuery->onlyTrashed();
        }
        
        // Gunakan distinct('mata_kuliahs.id') agar ID yang sama tidak dihitung dua kali
        $totalSemuaMK         = (clone $baseQuery)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');
        $totalTatapMuka       = (clone $baseQuery)->where('tipe_sks', 1)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');
        $totalPraktikum       = (clone $baseQuery)->where('tipe_sks', 2)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');
        $totalPraktekLapangan = (clone $baseQuery)->where('tipe_sks', 3)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');
        $totalSimulasi        = (clone $baseQuery)->where('tipe_sks', 4)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');

        // Lakukan hal yang sama untuk stats detail
        $totalAllOpsi = (clone $query)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');
        $totalWajib   = (clone $query)->where('is_wajib', true)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');
        $totalPilihan = (clone $query)->where('is_wajib', false)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');
        $totalUni     = (clone $query)->where('level_mk', 4)->distinct('mata_kuliahs.id')->count('mata_kuliahs.id');

        $this->buttonMKFilter($query);

        return view('livewire.staff.mk-management', [
            'mks' => $query->paginate($this->perPage),
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
