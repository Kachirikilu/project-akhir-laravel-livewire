<?php

namespace App\Livewire\Admin;

use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;

use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;

use App\Livewire\Admin\ProdiManagement\WithProdiModal;
use App\Livewire\Admin\ProdiManagement\WithProdiDelete;

use App\Models\ProgramStudi\Prodi;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Fakultas;

use Livewire\Component;
use Livewire\WithPagination;

class ProgramStudiManagement extends Component
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

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshProdisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterPr' => ['except' => ''],
        'switchTable' => ['except' => 'prodi'],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc']
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterPr']);
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
                $this->filterPr = '';
            } elseif ($table === 'fakultas') {
                $this->sortField = 'fakultas';
                $this->filterPr = '';
            }
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);

        if ($table == 'fakultas' && $this->perPage > 10) {
            $this->perPage = 10;
        }
        if ($table == 'jurusan' && $this->perPage > 50) {
            $this->perPage = 50;
        }

        $this->resetPage();
    }


    public function buttonStrataFilter($queryPr)
    {
        if (in_array($this->filterPr, ['sarjana', 'magister', 'doktor'])) {
            $queryPr->where('nama_strata', ucfirst($this->filterPr));
        }
    }

    public function render()
    {
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        $queryProdi = $this->inputProdiSearch();
        $queryJurusan = $this->inputJurusanSearch();
        $queryFakultas = $this->inputFakultasSearch();

        $queryPr = clone $queryProdi;
        $queryJr = clone $queryJurusan;
        $queryFk = clone $queryFakultas;

        $this->buttonStrataFilter($queryPr);

        $prodis = collect();
        $jurusans = collect();
        $fakultas = collect();

        if ($this->showDeleted) {
            $queryProdi->onlyTrashed();
            $queryJurusan->onlyTrashed();
            $queryFakultas->onlyTrashed();

            $queryPr->onlyTrashed();
            $queryJr->onlyTrashed();
            $queryFk->onlyTrashed();
        }

        if ($this->switchTable === 'prodi') {
            $prodis = $queryPr->paginate($this->perPage);
        } elseif ($this->switchTable === 'jurusan') {
            $jurusans = $queryJr->paginate($this->perPage);
        } elseif ($this->switchTable === 'fakultas') {
            $fakultas = $queryFk->paginate($this->perPage);
        }

        return view('livewire.admin.prodi-management', [
            'prodis' => $prodis,
            'jurusans' => $jurusans,
            'fakultas' => $fakultas,
            // 'totalProdis' => Prodi::count(),
            // 'totalSarjanas' => Prodi::where('nama_strata', 'Sarjana')->count(),
            // 'totalMagisters' => Prodi::where('nama_strata', 'Magister')->count(),
            // 'totalDoktors' => Prodi::where('nama_strata', 'Doktor')->count(),
            // 'totalJurusan' => Jurusan::count(),
            // 'totalFakultas' => Fakultas::count()
            'totalProdis' => (clone $queryProdi)->count(),
            'totalSarjanas' => (clone $queryProdi)->where('nama_strata', 'Sarjana')->count(),
            'totalMagisters' => (clone $queryProdi)->where('nama_strata', 'Magister')->count(),
            'totalDoktors' => (clone $queryProdi)->where('nama_strata', 'Doktor')->count(),
            'totalJurusan' => (clone $queryJurusan)->count(),
            'totalFakultas' => (clone $queryFakultas)->count()
        ]);
    }
}
