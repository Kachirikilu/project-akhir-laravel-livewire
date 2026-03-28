<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Staff\RPSManagement\WithCPLFilters;
use App\Livewire\Staff\RPSManagement\WithCPMKFilters;
use App\Livewire\Staff\RPSManagement\WithDosenFilters;
use App\Livewire\Staff\RPSManagement\WithReferensiFilters;
use App\Livewire\Staff\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\RPSManagement\WithSubCPMKFilters;
use App\Models\Akademik\Cpl;
use App\Models\Akademik\Cpmk;
use App\Models\Akademik\Rps;
use Livewire\Component;
use Livewire\WithPagination;

class RpsManagement extends Component
{
    use WithCPLFilters;
    use WithCPMKFilters;
    use WithDosenFilters;
    use WithFakultasSearchFilters;
    use WithJurusanSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithReferensiFilters;
    use WithRPSFilters;
    use WithSubCPMKFilters;

    public $switchTable = 'rps';

    public $perPage = 8;

    public $search = '';

    public $filter = '';

    public $showDeleted = false;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'switchTable' => ['except' => 'rps'],
        'filter' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc']
    ];

    public function updatedSearch()
    {
        $this->resetPage();
     }

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'rps'   => [1 => 'id', 2 => 'kode', 3 => 'matkul', 4 => 'tahun_akademik', 5 => 'status', 6 => 'tanggal_revisi'],
            'cpmk'  => [1 => 'id', 2 => 'kode', 3 => 'deskripsi'],
            'scpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'materi', 5 => 'indikator', 6 => 'bobot'],
            'cpl'   => [1 => 'id', 2 => 'kode', 3 => 'deskripsi'],
            'ref'   => [1 => 'id', 2 => 'kode', 3 => 'judul', 4 => 'penulis', 5 => 'penerbit', 6 => 'tahun', 7 => 'link'],
        ];

        if (!isset($columns[$table])) return;

        $targetCols = $columns[$table];

        $currentPos = 1;
        foreach ($columns as $cols) {
            $pos = array_search($sortField, $cols);
            if ($pos !== false) {
                $currentPos = $pos;
                break;
            }
        }

        $maxPosInTarget = max(array_keys($targetCols));
        $finalPos = min($currentPos, $maxPosInTarget);

        $this->sortField = $targetCols[$finalPos];
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

        $queryRPS   = $this->inputRPSSearch();
        // $queryRPS = $this->inputRPSSearch()
        // ->when($this->showDeleted, fn($q) => $q->onlyTrashed());
        // $totalRPS = (clone $queryRPS)->distinct('rps.id')->count('rps.id');

        $baseData = $this->inputRPSSearch()
            ->when($this->showDeleted, fn($q) => $q->onlyTrashed())
            ->get(['rps.id', 'rps.mk_id', 'rps.tahun_akademik', 'rps.is_draf', 'rps.tanggal_revisi'])
            ->unique('id');

        $queryCPMK  = $this->inputCPMKSearch();
        $querySCPMK = $this->inputSCPMKSearch();
        $queryCPL   = $this->inputCPLSearch();
        $queryRef   = $this->inputRefSearch();
        $queryDosen = $this->inputDosenSearch();

        $queryCPMK2 = clone $queryCPMK;
        $querySCPMK2 = clone $querySCPMK;
        $queryCPL2 = clone $queryCPL;
        $queryRef2 = clone $queryRef;
        $queryDosen2 = clone $queryDosen;

        // $queryJr = clone $queryJurusan;
        // $queryFk = clone $queryFakultas;

        if ($this->showDeleted) {
            $queryRPS->onlyTrashed();
            $queryCPMK->onlyTrashed();
            $querySCPMK->onlyTrashed();
            $queryCPL->onlyTrashed();
            $queryRef->onlyTrashed();

            $queryCPMK2->onlyTrashed();
            $querySCPMK2->onlyTrashed();
            $queryCPL2->onlyTrashed();
            $queryRef2  ->onlyTrashed();
            // $queryDosen->onlyTrashed();
        }

        $data = [
            'rps' => collect(),
            'cpmk' => collect(),
            'scpmk' => collect(),
            'cpl' => collect(),
            'ref' => collect()
            // 'dosen' => collect()
        ];

        switch ($this->switchTable) {
            case 'rps':
                $data['rps'] = $queryRPS->paginate($this->perPage);
                break;
            case 'cpmk':
                $data['cpmk'] = $queryCPMK2->paginate($this->perPage);
                break;
            case 'scpmk':
                $data['scpmk'] = $querySCPMK2->paginate($this->perPage);
                break;
            case 'cpl':
                $data['cpl'] = $queryCPL2->paginate($this->perPage);
                break;
            case 'ref':
                $data['ref'] = $queryRef2->paginate($this->perPage);
                break;
            case 'dosen':
                $data['dosen'] = $queryDosen2->paginate($this->perPage);
                break;
        }

        return view('livewire.staff.rps-management', array_merge($data, [
            'totalRPS'   => $baseData->count(),
            'totalCPMK'  => (clone $queryCPMK)->reorder()->count(),
            'totalSCPMK' => (clone $querySCPMK)->reorder()->count(),
            'totalCPL'   => (clone $queryCPL)->reorder()->count(),
            'totalRef'   => (clone $queryRef)->reorder()->count()
            // 'totalDosen' => (clone $queryDosen)->reorder()->count(),
        ]));
    }
}
