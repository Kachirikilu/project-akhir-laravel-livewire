<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;

use App\Livewire\Staff\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\RPSManagement\WithCPMKFilters;
use App\Livewire\Staff\RPSManagement\WithSubCPMKFilters;
use App\Livewire\Staff\RPSManagement\WithCPLFilters;
use App\Livewire\Staff\RPSManagement\WithReferensiFilters;
use App\Livewire\Staff\RPSManagement\WithDosenFilters;

use App\Livewire\Staff\RPSManagement\WithRPSModal;

use App\Models\Akademik\Rps;
use App\Models\Akademik\Cpmk;
use App\Models\Akademik\Cpl;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class RPSManagement extends Component
{
    use WithRPSFilters;
    use WithCPMKFilters;
    use WithSubCPMKFilters;
    use WithCPLFilters;
    use WithReferensiFilters;
    use WithDosenFilters;

    use WithFakultasSearchFilters;
    use WithJurusanSearchFilters;
    use WithProdiSearchFilters;
    
    use WithMKSearchFilters;
    use WithRPSSearchFilters;
    use WithCPMKSearchFilters;
    use WithSubCPMKSearchFilters;
    use WithCPLSearchFilters;
    use WithReferensiSearchFilters;
    use WithDosenSearchFilters;

    use WithRPSModal;

    use WithPagination;


    public $switchTable = 'rps';

    public $perPage = 8;

    public $search = '';

    public $showDeleted = false;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'switchTable' => ['except' => 'rps'],
        'filterRPS' => ['except' => ''],
        'filterCPMK' => ['except' => ''],
        'filterSCPMK' => ['except' => ''],
        'filterCPL' => ['except' => ''],
        'filterRef' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function resetInputFilter()
    {
        $this->reset(['search', 'filterRPS', 'filterCPMK', 'filterSCPMK', 'filterCPL', 'filterRef']);
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'mk', 4 => 'akademik', 5 => 'is_draf', 6 => 'revisi', 7 => 'created_at', 8 => 'updated_at'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'created_at', 5 => 'updated_at'],
            'scpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'materi', 5 => 'bobot', 6 => 'indikator', 7 => 'created_at', 8 => 'updated_at'],
            'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'created_at', 5 => 'updated_at'],
            'ref' => [1 => 'id', 2 => 'kode', 3 => 'judul', 4 => 'penulis', 5 => 'penerbit', 6 => 'tahun', 7 => 'link', 8 => 'created_at', 9 => 'updated_at']
        ];

        if (!isset($columns[$table])) {
            return;
        }

        $aliases = [
            'deskripsi' => ['mk', 'deskripsi', 'judul'],
            'materi'    => ['materi', 'mk', 'penulis'],
            'akademik'  => ['akademik', 'bobot'],
            'is_draf'   => ['is_draf', 'indikator'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at']
        ];

        $normalizedField = $sortField;
        foreach ($aliases as $master => $related) {
            if (in_array($sortField, $related)) {
                $normalizedField = $master; 
                break;
            }
        }

        $targetCols = $columns[$table];
        $currentPos = 1;

        foreach ($columns as $tableName => $cols) {
            foreach ($cols as $pos => $colName) {
                $isMatch = ($colName === $normalizedField);
                
                if (!$isMatch) {
                    foreach ($aliases as $master => $related) {
                        if (in_array($colName, $related) && in_array($normalizedField, $related)) {
                            $isMatch = true;
                            break;
                        }
                    }
                }

                if ($isMatch) {
                    $currentPos = $pos;
                    break 2; 
                }
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

        if ($table == 'cpl' && $this->perPage > 100) {
            $this->perPage = 100;
        }
        if ($table == 'ref' && $this->perPage > 150) {
            $this->perPage = 150;
        }
        if ($table == 'rps' && $this->perPage > 200) {
            $this->perPage = 200;
        }
        if ($table == 'cpmk' && $this->perPage > 300) {
            $this->perPage = 300;
        }
    }


    public function render()
    {
        $this->inputMKFilter();
        $this->inputRPSFilter();
        $this->inputCPMKFilter();
        $this->inputSCPMKFilter();
        $this->inputCPLFilter();
        $this->inputRefFilter();
        $this->inputDosenFilter();

        $this->inputProdiFilter();
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        $queryRPS = $this->inputRPSSearch();
        $baseDataRPS = $this->inputRPSSearch()
            ->when($this->showDeleted, fn ($q) => $q->onlyTrashed())
            ->get(['rps.id', 'rps.mk_id', 'rps.akademik', 'rps.is_draf', 'rps.tanggal_revisi'])
            ->unique('id');

        $queryCPMK = $this->inputCPMKSearch();
        $querySCPMK = $this->inputSCPMKSearch();
        $queryCPL = $this->inputCPLSearch();
        $queryRef = $this->inputRefSearch();
        // $queryDosen = $this->inputDosenSearch();

        $baseDataCPMK = (clone $queryCPMK)->get(['id', 'created_at']);
        $baseDataCPL = (clone $queryCPL)->get(['id', 'created_at']);
        $baseDataSCPMK = (clone $querySCPMK)->get(['id', 'created_at']);
        $baseDataCPL = (clone $queryCPL)->get(['id', 'created_at']);
        $baseDataRef = (clone $queryRef)->get(['id', 'created_at']);

        // $queryCPMK2 = clone $queryCPMK;
        // $querySCPMK2 = clone $querySCPMK;
        // $queryCPL2 = clone $queryCPL;
        // $queryRef2 = clone $queryRef;
        // $queryDosen2 = clone $queryDosen;

        // $queryJr = clone $queryJurusan;
        // $queryFk = clone $queryFakultas;

        if ($this->showDeleted) {
            $queryRPS->onlyTrashed();
            $queryCPMK->onlyTrashed();
            $querySCPMK->onlyTrashed();
            $queryCPL->onlyTrashed();
            $queryRef->onlyTrashed();

            // $queryCPMK2->onlyTrashed();
            // $querySCPMK2->onlyTrashed();
            // $queryCPL2->onlyTrashed();
            // $queryRef2  ->onlyTrashed();
            // $queryDosen->onlyTrashed();
        }

        $data = [
            'rps' => collect(),
            'cpmk' => collect(),
            'scpmk' => collect(),
            'cpl' => collect(),
            'ref' => collect(),
            // 'dosen' => collect()
        ];

        $now = Carbon::now();
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $currentYear = Carbon::now()->year;
        $threeYearsAgo = Carbon::now()->subYears(3);
        $fiveYearsAgo = Carbon::now()->subYears(5);
        $tenYearsAgo = Carbon::now()->subYears(10);

        $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
        $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
        $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
        $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
        $this->buttonRefFilter($queryRef, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);

        switch ($this->switchTable) {
            case 'rps':
                $data['rps'] = $queryRPS->paginate($this->perPage);
                break;
            case 'cpmk':
                $data['cpmk'] = $queryCPMK->paginate($this->perPage);
                break;
            case 'scpmk':
                $data['scpmk'] = $querySCPMK->paginate($this->perPage);
                break;
            case 'cpl':
                $data['cpl'] = $queryCPL->paginate($this->perPage);
                break;
            case 'ref':
                $data['ref'] = $queryRef->paginate($this->perPage);
                break;
                // case 'dosen':
                //     $data['dosen'] = $queryDosen->paginate($this->perPage);
                //     break;
        }

        $stats = [
            'rps-akademik' => 0, 'rps-ref-new' => 0, 'rps-aktif' => 0, 'rps-draf' => 0, 'rps-5-years' => 0,
            'cpmk-month' => 0, 'cpmk-6-months' => 0, 'cpmk-year' => 0, 'cpmk-5-years' => 0,
            'scpmk-month' => 0, 'scpmk-6-months' => 0, 'scpmk-year' => 0, 'scpmk-5-years' => 0,
            'cpl-month' => 0, 'cpl-6-months' => 0, 'cpl-year' => 0, 'cpl-5-years' => 0,
            'ref-year' => 0, 'ref-3-years' => 0, 'ref-5-years' => 0, 'ref-10-years' => 0,
        ];

        // switch ($this->switchTable) {
        //     case 'rps':
        $stats['rps-akademik'] = $baseDataRPS->filter(fn ($item) => str_contains($item->akademik, (string) $currentYear)
        )->count();

        $stats['rps-ref-new'] = $baseDataRPS->filter(fn($item) => 
                $item->tanggal_revisi && Carbon::parse($item->tanggal_revisi)->year == $currentYear
            )->count();


        $stats['rps-aktif'] = $baseDataRPS->where('is_draf', false)->count();
        $stats['rps-draf'] = $baseDataRPS->where('is_draf', true)->count();

        $stats['rps-5-years'] = $baseDataRPS->filter(function ($item) use ($fiveYearsAgo) {
            $startYear = (int) substr($item->akademik, 0, 4);

            // Sesuaikan: >= untuk 5 tahun terakhir, < untuk arsip lama
            return $startYear >= $fiveYearsAgo->year;
        })->count();

        $stats['rps-old'] = $baseDataRPS->filter(function ($item) use ($fiveYearsAgo) {
            $startYear = (int) substr($item->akademik, 0, 4);

            return $startYear < $fiveYearsAgo->year;
        })->count();
        //     break;

        // case 'cpmk':
        // Contoh untuk CPMK (berlaku sama untuk SCPMK dan CPL)
        $stats['cpmk-month'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->isSameMonth($now) && $item->created_at->year == $currentYear
        )->count();

        $stats['cpmk-6-months'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->greaterThanOrEqualTo($sixMonthsAgo)
        )->count();

        $stats['cpmk-year'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->year == $currentYear
        )->count();

        $stats['cpmk-5-years'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->greaterThanOrEqualTo($fiveYearsAgo)
        )->count();

        $stats['cpmk-old'] = $baseDataCPMK->filter(fn ($item) => $item->created_at && $item->created_at->lessThan($fiveYearsAgo)
        )->count();
        // break;

        // case 'scpmk':
        $stats['scpmk-month'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->isSameMonth($now) && $item->created_at->year == $currentYear
        )->count();
        $stats['scpmk-6-months'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->greaterThanOrEqualTo($sixMonthsAgo)
        )->count();
        $stats['scpmk-year'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->year == $currentYear
        )->count();
        $stats['scpmk-5-years'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->greaterThanOrEqualTo($fiveYearsAgo)
        )->count();
        $stats['scpmk-old'] = $baseDataSCPMK->filter(fn ($item) => $item->created_at && $item->created_at->lessThan($fiveYearsAgo)
        )->count();
        // break;

        // case 'cpl':
        $stats['cpl-month'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->isSameMonth($now) &&
            Carbon::parse($item->created_at)->year == $currentYear
        )->count();
        $stats['cpl-6-months'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->greaterThanOrEqualTo($sixMonthsAgo)
        )->count();
        $stats['cpl-year'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->year == $currentYear
        )->count();
        $stats['cpl-5-years'] = $baseDataCPL->filter(fn ($item) => $item->created_at && Carbon::parse($item->created_at)->lessThan($fiveYearsAgo)
        )->count();
        //  break;
        // default:

        // Tahun ini
        $stats['ref-year'] = $baseDataRef->where('tahun', $currentYear)->count();

        // 3 Tahun Terakhir (Antara 3 tahun lalu s/d tahun lalu)
        $stats['ref-3-years'] = $baseDataRef->whereBetween('tahun', [$threeYearsAgo->year, $currentYear - 1])->count();

        // 5 Tahun Terakhir (Antara 5 tahun lalu s/d 4 tahun lalu)
        $stats['ref-5-years'] = $baseDataRef->whereBetween('tahun', [$fiveYearsAgo->year, $threeYearsAgo->year - 1])->count();

        // 10 Tahun Terakhir (Antara 10 tahun lalu s/d 6 tahun lalu)
        $stats['ref-10-years'] = $baseDataRef->whereBetween('tahun', [$tenYearsAgo->year, $fiveYearsAgo->year - 1])->count();

        // Tambahan: Referensi Jadul (Di bawah 10 tahun)
        $stats['ref-old'] = $baseDataRef->where('tahun', '<', $tenYearsAgo->year)->count();

        return view('livewire.staff.rps-management', array_merge($data, [
            'totalRPS' => $baseDataRPS->count(),
            'totalCPMK' => $baseDataCPMK->count(),
            'totalSCPMK' => $baseDataSCPMK->count(),
            'totalCPL' => $baseDataCPL->count(),
            'totalRef' => $baseDataRef->count(),

            'stats' => $stats,
        ]));
    }
}
