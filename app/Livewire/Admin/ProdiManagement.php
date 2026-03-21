<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\GlobalManagement\WithJurusanSearchFilters;
use App\Livewire\Admin\GlobalManagement\WithFakultasSearchFilters;

use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;

use App\Livewire\Admin\ProdiManagement\WithProdiModal;
use App\Livewire\Admin\ProdiManagement\WithProdiDelete;

use App\Models\Prodi;
use App\Models\Jurusan;
use App\Models\Fakultas;

use Livewire\Component;
use Livewire\WithPagination;

class ProdiManagement extends Component
{
    use WithJurusanSearchFilters;
    use WithFakultasSearchFilters;

    use WithProdiFilters;
    use WithJurusanFilters;
    use WithFakultasFilters;
    
    use WithProdiModal;
    use WithProdiDelete;

    use WithPagination;

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
        'sortField' => ['except' => 'prodi'],
        'sortDirection' => ['except' => 'asc'],

        'selectedProdiId' => ['except' => null],
        'selectedJurusanId' => ['except' => null],
        'selectedFakultasId' => ['except' => null]
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function refreshProdisList()
    {
        $this->resetPage();
    }

    private function syncSortField($table, $sortField)
    {
        if ($sortField != 'id' && $sortField != 'kode') {
            if ($table === 'prodi') {
                $this->sortField = 'prodi';
            } elseif ($table === 'jurusan') {
                $this->sortField = 'jurusan';
                $this->filter = '';
            } elseif ($table === 'fakultas') {
                $this->sortField = 'fakultas';
                $this->filter = '';
            }
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);

        if ($table == 'jurusan' && $this->perPage > 50) {
            $this->perPage = 50;
        }
        if ($table == 'fakultas' && $this->perPage > 10) {
            $this->perPage = 10;
        }

        $this->resetPage();
    }

    // public function render()
    // {
    //     $query = $this->inputMainSearch();
    //     $queryJurusan = $this->inputJurusanSearch();
    //     $queryFakultas = $this->inputFakultasSearch();

    //     $countTotal = $this->buttonStrataFilter($query);

    //     if ($this->switchTable === 'jurusan' && $this->sortField === 'prodi') {
    //         $this->sortField = 'jurusan';
    //         if ($this->perPage > 50) {
    //             $this->perPage = 50;
    //         }
    //     } elseif ($this->switchTable === 'fakultas' && $this->sortField === 'prodi') {
    //         $this->sortField = 'fakultas';
    //         if ($this->perPage > 10) {
    //             $this->perPage = 10;
    //         }
    //     }

    //     $this->inputFakultasFilter();
    //     $this->inputJurusanFilter();

    //     $totalJurusanCount = (clone $queryJurusan)->count();
    //     $totalFakultasCount = (clone $queryFakultas)->count();

    //     return view('livewire.admin.prodi-management', [
    //         'prodis' => $query->paginate($this->perPage),
    //         'jurusans' => $queryJurusan->paginate($this->perPage),
    //         'fakultass' => $queryFakultas->paginate($this->perPage),
    //         'totalProdis' => $countTotal[0],
    //         'totalSarjanas' => $countTotal[1],
    //         'totalMagisters' => $countTotal[2],
    //         'totalDoktors' => $countTotal[3],
    //         'totalJurusan' => $totalJurusanCount,
    //         'totalFakultas' => $totalFakultasCount
    //     ]);
    // }

    public function buttonStrataFilter($query)
    {
        if (in_array($this->filter, ['sarjana', 'magister', 'doktor'])) {
            $query->where('nama_strata', ucfirst($this->filter));
        }
    }

    public function render()
    {
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        $baseQuery = $this->inputMainSearch();
        $query = clone $baseQuery;

        $this->buttonStrataFilter($query);

        $prodis = collect();
        $jurusans = collect();
        $fakultass = collect();

        $queryJurusan = $this->inputJurusanSearch();
        $queryFakultas = $this->inputFakultasSearch();

        if ($this->switchTable === 'prodi') {
            $prodis = $query->paginate($this->perPage);
        } elseif ($this->switchTable === 'jurusan') {
            $jurusans = $queryJurusan->paginate($this->perPage);
        } elseif ($this->switchTable === 'fakultas') {
            $fakultass = $queryFakultas->paginate($this->perPage);
        }

        return view('livewire.admin.prodi-management', [
            'prodis' => $prodis,
            'jurusans' => $jurusans,
            'fakultass' => $fakultass,
            // 'totalProdis' => Prodi::count(),
            // 'totalSarjanas' => Prodi::where('nama_strata', 'Sarjana')->count(),
            // 'totalMagisters' => Prodi::where('nama_strata', 'Magister')->count(),
            // 'totalDoktors' => Prodi::where('nama_strata', 'Doktor')->count(),
            // 'totalJurusan' => Jurusan::count(),
            // 'totalFakultas' => Fakultas::count()
            'totalProdis' => (clone $baseQuery)->count(),
            'totalSarjanas' => (clone $baseQuery)->where('nama_strata', 'Sarjana')->count(),
            'totalMagisters' => (clone $baseQuery)->where('nama_strata', 'Magister')->count(),
            'totalDoktors' => (clone $baseQuery)->where('nama_strata', 'Doktor')->count(),
            'totalJurusan' => (clone $queryJurusan)->count(),
            'totalFakultas' => (clone $queryFakultas)->count()
        ]);
    }
}
