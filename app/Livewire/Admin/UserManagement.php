<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\ProdiManagement\WithDepartemenFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserExcel;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Models\Auth\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithDepartemenFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasFilters;
    use WithFakultasSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithUserDelete;
    use WithUserExcel;
    use WithUserFilters;
    use WithUserModal;

    public $showModal = false;

    public $perPage = 8;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $switchTable = '';

    protected $listeners = ['refresh-table' => 'refreshUsersList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    public $showDeleted = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'switchTable' => ['except' => ''],
        'filterStatus' => ['except' => ''],

        // 'pr_name' => ['except' => ''],
        // 'roleType' => ['except' => ''],
        // 'isEditing' => ['except' => false],
        // 'showUserModal' => ['except' => false],
        // 'userId' => ['except' => ''],
        // 'email' => ['except' => ''],
        // 'name' => ['except' => ''],
        // 'nip' => ['except' => ''],
        // 'nim' => ['except' => ''],
        // 'angkatan' => ['except' => ''],
        // 'pr_id' => ['except' => ''],
        // 'prNameSearch' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'switchTable']);
        $this->resetPage();
    }

    public function refreshUsersList()
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
        $this->resetPage();
    }

    private function syncSortField($filter, $sortField)
    {
        if ($filter != '' && $sortField == 'role') {
            $this->sortField = 'name';
        } elseif ($filter != 'mahasiswa' && $sortField == 'angkatan') {
            $this->sortField = 'status';
        } elseif ($filter == 'mahasiswa' && $sortField == 'identity2') {
            $this->sortField = 'identity1';
        } elseif ($filter != 'dosen' && $sortField == 'identity3') {
            if ($filter == 'mahasiswa') {
                $this->sortField = 'identity1';
            } elseif ($filter == 'dosen') {
                $this->sortField = 'identity2';
            }
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();
    }


    public function render()
    {
        $this->inputPrFilter();
        $this->inputDpFilter();
        $this->inputFkFilter();

        try {

            // =========================
            // 1. BASE MURNI (JANGAN DIUBAH)
            // =========================
            $baseUser = User::query();

            // =========================
            // 2. TABLE QUERY (TERPENGARUH FILTER UI)
            // =========================
            $tableQuery = clone $baseUser;

            $this->buttonRoleFilter($tableQuery);

            if (! empty($this->switchTable)) {
                $tableQuery->whereHas($this->switchTable);
            }

            if ($this->showDeleted) {
                $tableQuery->onlyTrashed();
            }

            // =========================
            // 3. STATS QUERY (SELALU CLEAN)
            // =========================
            $statsAll = User::query();
            $statsAktif = User::query();
            $statsNonAktif = User::query();

            // =========================
            // SWITCH TABLE APPLY KE STATS (HANYA ROLE FILTER)
            // =========================
            if (! empty($this->switchTable)) {
                $statsAll->whereHas($this->switchTable);
                $statsAktif->whereHas($this->switchTable);
                $statsNonAktif->whereHas($this->switchTable);
            }

            if ($this->showDeleted) {
                $statsAll->onlyTrashed();
                $statsAktif->onlyTrashed();
                $statsNonAktif->onlyTrashed();
            }

            // =========================
            // STATUS FILTER DIPISAH PER STAT (INI KUNCI 🔥)
            // =========================
            $statsAktif->where(function ($q) {
                $q->whereHas('admin', fn ($s) => $s->where('status', 'Aktif'))
                    ->orWhereHas('dosen', fn ($s) => $s->where('status', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Aktif'));
            });

            $statsNonAktif->where(function ($q) {
                $q->whereHas('admin', fn ($s) => $s->where('status', '!=', 'Aktif'))
                    ->orWhereHas('dosen', fn ($s) => $s->where('status', '!=', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', '!=', 'Aktif'));
            });

            // =========================
            // RESULT VIEW
            // =========================
            return view('livewire.admin.user-management', [
                'users' => $tableQuery->paginate($this->perPage),

                'totalAllOpsi' => $statsAll->count(),
                'totalAktif' => $statsAktif->count(),
                'totalNonAktif' => $statsNonAktif->count(),

                'totalUsers' => User::count(),
                'totalAdmins' => User::whereHas('admin')->count(),
                'totalDosens' => User::whereHas('dosen')->count(),
                'totalMahasiswas' => User::whereHas('mahasiswa')->count(),
            ]);

        } catch (QueryException $e) {

            $this->toast(text: 'Terjadi kesalahan database: '.$e->getMessage(), variant: 'danger');

            return view('livewire.admin.user-management', [
                'users' => User::whereRaw('1=0')->paginate($this->perPage),

                'totalAllOpsi' => '-',
                'totalAktif' => '-',
                'totalNonAktif' => '-',
            ]);
        }
    }
}
