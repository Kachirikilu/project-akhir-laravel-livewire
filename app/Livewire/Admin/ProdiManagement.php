<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasSearchFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanSearchFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiDelete;
use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiModal;
// use App\Livewire\Admin\ProdiManagement\WithProdiExcel;

// use App\Models\Jurusan;
// use App\Models\Prodi;
// use App\Models\Fakultas;

use Livewire\Component;
use Livewire\WithPagination;

class ProdiManagement extends Component
{
    use WithFakultasFilters;
    use WithFakultasSearchFilters;
    use WithJurusanFilters;
    use WithJurusanSearchFilters;
    use WithPagination;
    use WithProdiFilters;

    use WithProdiModal;
    use WithProdiDelete;
    // use WithProdiExcel;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = 'prodi';

    protected $paginationTheme = 'tailwind';

    public $selectedProdiName = '';

    protected $listeners = ['refresh-table' => 'refreshProdisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'selectedProdiName' => ['except' => ''],
        'switchTable' => ['except' => 'prodi'],
    ];

    public function mount()
    {
        $this->syncSortField($this->switchTable);
    }

    public function syncSortField($table)
    {
        if ($table === 'prodi') {
            $this->sortField = 'prodi';
        } elseif ($table === 'jurusan') {
            $this->sortField = 'jurusan';
        } elseif ($table === 'fakultas') {
            $this->sortField = 'fakultas';
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function refreshProdisList()
    {
        $this->resetPage();
    }

    // public function switchingTable($table)
    // {
    //     $this->switchTable = $table;

    //     if ($table === 'prodi') {
    //         $this->sortField = 'prodi';
    //     } elseif ($table == 'jurusan') {
    //         $this->sortField = 'jurusan';
    //         if ($this->perPage > 50) {
    //             $this->perPage = 50;
    //         }
    //     } elseif ($table == 'fakultas') {
    //         $this->sortField = 'fakultas';
    //         if ($this->perPage > 10) {
    //             $this->perPage = 10;
    //         }
    //     }

    //     $this->resetPage();
    // }
    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table);

        // Logika limit perPage tetap dipertahankan
        if ($table == 'jurusan' && $this->perPage > 50) $this->perPage = 50;
        if ($table == 'fakultas' && $this->perPage > 10) $this->perPage = 10;

        $this->resetPage();
    }

    public function render()
    {
        $query = $this->inputMainSearch();
        $queryJurusan = $this->inputJurusanSearch();
        $queryFakultas = $this->inputFakultasSearch();

        $countTotal = $this->buttonStrataFilter($query);

        if ($this->switchTable === 'jurusan' && $this->sortField === 'prodi') {
            $this->sortField = 'jurusan';
            if ($this->perPage > 50) {
                $this->perPage = 50;
            }
        } elseif ($this->switchTable === 'fakultas' && $this->sortField === 'prodi') {
            $this->sortField = 'fakultas';
            if ($this->perPage > 10) {
                $this->perPage = 10;
            }
        }

        $this->inputFakultasFilter();
        $this->inputJurusanFilter();

        $totalJurusanCount = (clone $queryJurusan)->count();
        $totalFakultasCount = (clone $queryFakultas)->count();

        return view('livewire.admin.prodi-management', [
            'prodis' => $query->paginate($this->perPage),
            'jurusans' => $queryJurusan->paginate($this->perPage),
            'fakultass' => $queryFakultas->paginate($this->perPage),
            'totalProdis' => $countTotal[0],
            'totalSarjanas' => $countTotal[1],
            'totalMagisters' => $countTotal[2],
            'totalDoktors' => $countTotal[3],
            'totalJurusan' => $totalJurusanCount,
            'totalFakultas' => $totalFakultasCount
        ]);
    }
}
