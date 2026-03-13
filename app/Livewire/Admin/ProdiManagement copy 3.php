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

    protected $listeners = ['refresh-table' => 'refreshProdisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'selectedProdiName' => ['except' => ''],
        'switchTable' => ['except' => 'prodi'],
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function refreshProdisList()
    {
        $this->resetPage();
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;

        if ($table === 'prodi') {
            $this->sortField = 'prodi';
        } elseif ($table == 'jurusan') {
            $this->sortField = 'jurusan';
            // $this->resetInputFilter();
            if ($this->perPage > 50) {
                $this->perPage = 50;
            }
        } elseif ($table == 'fakultas') {
            $this->sortField = 'fakultas';
            // $this->resetInputFilter();
            // $this->resetJurusanFilter();
            if ($this->perPage > 10) {
                $this->perPage = 10;
            }
        }

        // if ($table == 'jurusan' && $this->sortField === 'prodi') {
        //     $this->sortField = 'jurusan';
        // } elseif ($table == 'fakultas' && ($this->sortField === 'prodi' || $this->sortField === 'jurusan')) {
        //     $this->sortField = 'fakultas';
        // }

        $this->resetPage();
    }

    // public function resetAllFilters()
    // {
    //     $this->resetInputFilter();
    //     $this->resetFakultasFilter();
    //     $this->resetJurusanFilter();
    //     $this->resetPage();
    // }

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

        // $totalProdiQuery = Prodi::query();
        // if (! empty($this->search)) {
        //     $totalProdiQuery->where(function ($q) {
        //         $q->where('nama_prodi', 'like', "%{$this->search}%")
        //             ->orWhere('id', $this->search);
        //     });
        // }

        // $totalJurusanQuery = Jurusan::query();
        // if (! empty($this->search)) {
        //     $totalJurusanQuery->where(function ($q) {
        //         $q->where('nama_jurusan', 'like', "%{$this->search}%")
        //             ->orWhere('id', $this->search)
        //             ->orWhereHas('fakultas_rel', function ($sq) {
        //                 $sq->where('nama_fakultas', 'like', "%{$this->search}%")
        //                     ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$this->search]);
        //             });
        //     });
        // }

        // $totalFakultasQuery = Fakultas::query();
        // if (! empty($this->search)) {
        //   $totalFakultasQuery->where(function ($q) {
        //       $q->where('nama_fakultas', 'like', "%{$this->search}%")
        //           ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", ["%{$this->search}%"]);
        //   });
        // }

        // $totalProdi = $totalProdiQuery->count();
        // $totalJurusan = $totalJurusanQuery->count();
        // $totalFakultas = $totalFakultasQuery->count();

        // if ($this->switchTable === 'prodi') {
        //     $totalProdi = $countTotal[0];
        // } elseif ($this->switchTable === 'jurusan') {
        //     $totalJurusan = $queryJurusan->count();
        // } elseif ($this->switchTable === 'fakultas') {
        //     $totalFakultas = $queryFakultas->count();
        // }

        // $totalProdi = $countTotal[0];
        // $totalJurusan = $queryJurusan->count();
        // $totalFakultas = $queryFakultas->count();
    
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
