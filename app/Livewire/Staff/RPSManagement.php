<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;

use App\Livewire\Staff\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\RPSManagement\WithCPMKFilters;
use App\Livewire\Staff\RPSManagement\WithSubCPMKFilters;
use App\Livewire\Staff\RPSManagement\WithCPLFilters;
use App\Livewire\Staff\RPSManagement\WithReferensiFilters;
use App\Livewire\Staff\RPSManagement\WithDosenFilters;


use App\Models\Akademik\Cpl;
use App\Models\Akademik\Cpmk;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\Rps;
use App\Models\Akademik\SubCpmk;
use Livewire\Component;
use Livewire\WithPagination;

class RpsManagement extends Component
{
    use WithProdiSearchFilters;
    use WithJurusanSearchFilters;
    use WithFakultasSearchFilters;

    use WithRPSFilters;
    use WithCPMKFilters;
    use WithSubCPMKFilters;
    use WithCPLFilters;
    use WithReferensiFilters;
    use WithDosenFilters;


    use WithPagination;

    public $switchTable = 'rps';

    public $perPage = 8;

    public $search = '';

    public $filter = ''; // Khusus RPS (draft, published)

    public $showDeleted = false;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'switchTable' => ['except' => 'rps'],
        'filter' => ['except' => ''],
        'showDeleted' => ['except' => false],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    private function syncSortField($table, $sortField)
    {
        if ($table == 'rps') {
            if (! in_array($sortField, ['tahun_akademik', 'status', 'created_at'])) {
                $this->sortField = 'created_at';
            }
        } elseif ($table == 'cpmk') {
            if (! in_array($sortField, ['kode_cpmk', 'digit_cpmk', 'deskripsi'])) {
                $this->sortField = 'kode_cpmk';
            }
        } elseif ($table == 'scpmk') {
            if (! in_array($sortField, ['kode_scpmk', 'digit_scpmk', 'bobot'])) {
                $this->sortField = 'kode_scpmk';
            }
        } elseif ($table == 'cpl') {
            if (! in_array($sortField, ['kode_cpl', 'deskripsi'])) {
                $this->sortField = 'kode_cpl';
            }
        } elseif ($table == 'ref') {
            if (! in_array($sortField, ['judul', 'penulis', 'tahun'])) {
                $this->sortField = 'judul';
            }
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();
    }

    // public function render()
    // {
    //     $this->inputProdiFilter();
    //     $this->inputJurusanFilter();
    //     $this->inputFakultasFilter();

    //     $query = $this->inputRPSSearch();

    //     if ($this->switchTable === 'rps') {
    //         // Asumsi RPS adalah default, bisa ditambahkan filter khusus jika perlu
    //     } elseif ($this->switchTable === 'cpmk') {
    //         // Jika model berbeda, pastikan inputRPSSearch() menghandle pergantian Model
    //     } elseif ($this->switchTable === 'scpmk') {
    //         // Logika filter scpmk
    //     }

    //     // 2. Logika Soft Deletes (Toggle Sampah)
    //     $queryRPS = $this->inputRPSSearch();
    //     $queryCPMK = $this->inputCPMKSearch();
    //     $querySCPMK = $this->inputSCPMKSearch();
    //     $queryCPL = $this->inputCPLSearch();
    //     $queryRef = $this->inputRefSearch();

    //     if ($this->showDeleted) {
    //         $query->onlyTrashed();
    //         $queryRPS->onlyTrashed();
    //     }

    //     $totalRPS = (clone $queryRPS)->count();
    //     $totalAktif = (clone $queryRPS)->where('status', 'published')->count();
    //     $totalDraf = (clone $queryRPS)->where('status', 'draft')->count();

    //     $countCPMK = Cpmk::count();
    //     $countSCPMK = SubCpmk::count();
    //     $countCPL = Cpl::count();
    //     $countRef = Referensi::count();

    //     // 4. Eksekusi Query dengan Pagination
    //     return view('livewire.staff.rps-management', [
    //         'items' => $query->paginate($this->perPage),
    //         'totalRPS' => $totalRPS,
    //         'totalAktif' => $totalAktif,
    //         'totalDraf' => $totalDraf,

    //         'totalCPMK' => $countCPMK,
    //         'totalSCPMK' => $countSCPMK,
    //         'totalCPL' => $countCPL,
    //         'totalRef' => $countRef,
    //     ]);
    // }

    public function render()
    {
        $this->inputProdiFilter();
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        // Ambil semua query secara terpisah untuk menghitung badge statistik
        $queryRPS = $this->inputRPSSearch();
        $queryCPMK = $this->inputCPMKSearch();
        $querySCPMK = $this->inputSCPMKSearch();
        $queryCPL = $this->inputCPLSearch();
        $queryRef = $this->inputRefSearch();
        $queryDosen = $this->inputDosenSearch();

        if ($this->showDeleted) {
            $queryRPS->onlyTrashed();
            $queryCPMK->onlyTrashed();
            $querySCPMK->onlyTrashed();
            $queryCPL->onlyTrashed();
            $queryRef->onlyTrashed();
            $queryDosen->onlyTrashed();
        }

        return view('livewire.staff.rps-management', [
            'rps' => $queryRPS->paginate($this->perPage),
            'cpmk' => $queryCPMK->paginate($this->perPage),
            'scpmk' => $querySCPMK->paginate($this->perPage),
            'cpl' => $queryCPL->paginate($this->perPage),
            'ref' => $queryRef->paginate($this->perPage),
            'dosen' => $queryDosen->paginate($this->perPage),

            'totalRPS' => (clone $queryRPS)->count(),
            'totalCPMK' => (clone $queryCPMK)->count(),
            'totalSCPMK' => (clone $querySCPMK)->count(),
            'totalCPL' => (clone $queryCPL)->count(),
            'totalRef' => (clone $queryRef)->count(),
            'totalDosen' => (clone $queryDosen)->count(),
        ]);
    }
}
