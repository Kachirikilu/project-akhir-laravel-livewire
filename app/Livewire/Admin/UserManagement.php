<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserExcel;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Models\Auth\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithFakultasFilters;
    use WithFakultasSearchFilters;
    use WithJurusanFilters;
    use WithJurusanSearchFilters;
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

    protected $listeners = ['refresh-table' => 'refreshUsersList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    public $showDeleted = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],

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

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterUser']);
        $this->resetPage();
    }

    public function refreshUsersList()
    {
        $this->resetPage();
    }

    public function buttonRoleFilter($query)
    {
        $query->when(in_array($this->filterUser, ['admin', 'dosen', 'mahasiswa']), function ($q) {
            return $q->whereHas($this->filterUser);
        });
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

    public function render()
    {
        $this->inputPrFilter();
        $this->inputJrFilter();
        $this->inputFkFilter();

        try {

            $this->syncSortField($this->filterUser, $this->sortField);

            $queryUser = $this->inputUserSearch();
            $this->buttonRoleFilter($queryUser);

            // =========================
            // BASE COUNT QUERY
            // =========================
            $countUser = User::query();

            if ($this->showDeleted) {
                $queryUser->onlyTrashed();
                $countUser->onlyTrashed();
            }

            return view('livewire.admin.user-management', [
                'users' => $queryUser->paginate($this->perPage),

                // =========================
                // COUNT (ISOLATED)
                // =========================
                'totalUsers' => (clone $countUser)->count(),
                'totalAdmins' => (clone $countUser)->whereHas('admin')->count(),
                'totalDosens' => (clone $countUser)->whereHas('dosen')->count(),
                'totalMahasiswas' => (clone $countUser)->whereHas('mahasiswa')->count(),
            ]);

        } catch (QueryException $e) {

            $this->toast(text: 'Terjadi kesalahan database: '.$e->getMessage(), variant: 'danger');

            return view('livewire.admin.user-management', [
                'users' => User::whereRaw('1=0')->paginate($this->perPage),

                'totalUsers' => 0,
                'totalAdmins' => 0,
                'totalDosens' => 0,
                'totalMahasiswas' => 0,
            ]);
        }
    }
}
