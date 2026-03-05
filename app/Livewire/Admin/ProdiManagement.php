<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasSearchFilters;
// use App\Livewire\Admin\ProdiManagement\WithProdiModal;
// use App\Livewire\Admin\ProdiManagement\WithProdiDelete;
// use App\Livewire\Admin\ProdiManagement\WithProdiExcel;

class ProdiManagement extends Component
{
    use WithPagination;
    
    use WithProdiFilters;
    use WithJurusanFilters;
    use WithFakultasFilters;
    use WithFakultasSearchFilters;

    // use WithProdiModal;
    // use WithProdiDelete;
    // use WithProdiExcel;

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
        } elseif ($table == 'fakultas') {
            $this->sortField = 'fakultas';
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
        } elseif ($this->switchTable === 'fakultas' && $this->sortField === 'prodi') {
            $this->sortField = 'fakultas';
        }

        $this->inputFakultasFilter();

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