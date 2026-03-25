<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasSearchFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanSearchFilters;
// use App\Livewire\Admin\ProdiManagement\WithProdiDelete;
// use App\Livewire\Admin\ProdiManagement\WithProdiExcel;

class ProdiManagement extends Component
{
    use WithPagination;
    
    use WithProdiFilters;
    use WithJurusanFilters;
    use WithFakultasFilters;
    use WithFakultasSearchFilters;
    use WithJurusanSearchFilters;

    // use WithProdiModal;
    // use WithProdiDelete;
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
        'prodi_name' => ['except' => ''],
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
        // $this->search = '';

        if ($table === 'prodi') {
            $this->sortField = 'prodi';
        } elseif ($table == 'jurusan') {
            $this->sortField = 'jurusan';
            $this->resetInputFilter();
            if ($this->perPage > 50) {
                $this->perPage = 50;
            }
        } elseif ($table == 'fakultas') {
            $this->sortField = 'fakultas';
            $this->resetInputFilter();
            $this->resetJurusanFilter();
            if ($this->perPage > 10) {
                $this->perPage = 10;
            }
        }
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

        return view('livewire.admin.prodi-management', [
            'prodis' => $query->paginate($this->perPage),
            'jurusans' => $queryJurusan->paginate($this->perPage),
            'fakultass' => $queryFakultas->paginate($this->perPage),
            'totalProdis' => $countTotal[0],
            'totalSarjanas' => $countTotal[1],
            'totalMagisters' => $countTotal[2],
            'totalDoktors' => $countTotal[3],
            'totalJurusan' => $queryJurusan->count(),
            'totalFakultas' => $queryFakultas->count(),
        ]);
    }

}