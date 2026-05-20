<?php

namespace App\Livewire\Staff\KelasManagement\JadwalManagement;

use App\Livewire\Staff\KelasManagement\JadwalManagement\SesiManagement\WithSesiFilters;
use App\Livewire\Staff\KelasManagement\JadwalManagement\SesiManagement\WithSesiModal;
use App\Livewire\Staff\RPSManagement\WithRPSShow;
use App\Livewire\Global\WithMahasiswaSearchFilters;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class SesiManagement extends Component
{
    use WithSesiFilters;
    use WithSesiModal;
    use WithRPSShow;
    use WithMahasiswaSearchFilters;

    use WithPagination;

    public $kode;

    public $kode_jadwal;

    public $kelas;

    public $jadwal;

    public $id_jadwal;

    public $perPage = 16;

    public $sortField = 'label_kelas';

    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 16],
        'sortField' => ['except' => 'label_kelas'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($kode, $kode_jadwal, $id_jadwal)
    {
        $this->kode = $kode;
        $this->kelas = Kelas::where('kode_kelas', $kode)
            ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
            ->firstOrFail();
        
        $this->id_jadwal = $id_jadwal;
        $this->jadwal = KelasJadwal::where('id', $id_jadwal)->firstOrFail();
        $this->kode_jadwal = $this->jadwal->kode_jadwal;
    }

    public function loadingTable() {}

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        try {
            $querySesi = $this->inputSesiSearch($this->id_jadwal);
            if ($this->showDeleted) {
                $querySesi->onlyTrashed();
            }

            return view('livewire.staff.kelas-management.jadwal-management.sesi-management', [
                'sesis' => $querySesi->paginate($this->perPage),
                'kelas' => $this->kelas,
            ]);

        } catch (QueryException $e) {
            session()->flash('error', 'Terjadi kesalahan database: '.$e->getMessage());

            return view('livewire.staff.kelas-management.jadwal-management.sesi-management', [
                'sesis' => KelasSesi::whereRaw('1 = 0')->paginate($this->perPage),
                'kelas' => $this->kelas,
            ]);
        }
    }
}
