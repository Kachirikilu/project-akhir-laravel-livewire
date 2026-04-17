<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiDelete;
use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiModal;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramStudiManagement extends Component
{
    use WithFakultasFilters;
    use WithFakultasSearchFilters;
    use WithJurusanFilters;
    use WithJurusanSearchFilters;
    use WithPagination;
    use WithProdiDelete;
    use WithProdiFilters;
    use WithProdiModal;

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
        'sortDirection' => ['except' => 'asc'],
    ];

    public function loadingTable() {}

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
            $queryPr->where('strata', ucfirst($this->filterPr));
        }
    }

    public function render()
    {
        $this->inputJrFilter();
        $this->inputFkFilter();

        $queryProdi = $this->inputProdiSearch();
        $queryJurusan = $this->inputJurusanSearch();
        $queryFakultas = $this->inputFkSearch();

        try {

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
                'totalProdis' => (clone $queryProdi)->count(),
                'totalSarjanas' => (clone $queryProdi)->where('strata', 'Sarjana')->count(),
                'totalMagisters' => (clone $queryProdi)->where('strata', 'Magister')->count(),
                'totalDoktors' => (clone $queryProdi)->where('strata', 'Doktor')->count(),
                'totalJurusan' => (clone $queryJurusan)->count(),
                'totalFakultas' => (clone $queryFakultas)->count(),
            ]);

        } catch (QueryException $e) {

            $this->toast(text: 'Terjadi kesalahan database: '.$e->getMessage(), variant: 'danger');

            return view('livewire.admin.prodi-management', [
                'prodis' => Prodi::whereRaw('1=0')->paginate($this->perPage),
                'jurusans' => Jurusan::whereRaw('1=0')->paginate($this->perPage),
                'fakultas' => Fakultas::whereRaw('1=0')->paginate($this->perPage),

                'totalProdis' => 0,
                'totalSarjanas' => 0,
                'totalMagisters' => 0,
                'totalDoktors' => 0,

                'totalJurusan' => 0,
                'totalFakultas' => 0,
            ]);
        }
    }
}
