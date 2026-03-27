<?php

namespace App\Livewire\Admin;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithJurusanSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;

use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;

use App\Livewire\Admin\UserManagement\WithUserExcel;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Admin\UserManagement\WithUserDelete;

use App\Models\Auth\User;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;

use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithProdiSearchFilters;
    use WithJurusanSearchFilters;
    use WithFakultasSearchFilters;

    use WithUserFilters;
    use WithJurusanFilters;
    use WithFakultasFilters;

    use WithUserExcel;
    use WithUserDelete;
    use WithUserModal;
    use WithPagination;

    public $showModal = false;

    public $perPage = 8;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['refresh-table' => 'refreshUsersList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    public $showDeleted = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted'  => ['except' => false]

        // 'prodi_name' => ['except' => ''],
        // 'roleType' => ['except' => ''],
        // 'isEditing' => ['except' => false],
        // 'showUserModal' => ['except' => false],
        // 'userId' => ['except' => ''],
        // 'email' => ['except' => ''],
        // 'name' => ['except' => ''],
        // 'nip' => ['except' => ''],
        // 'nim' => ['except' => ''],
        // 'tahun_angkatan' => ['except' => ''],
        // 'prodi_id' => ['except' => ''],
        // 'prodiNameSearch' => ['except' => ''],
    ];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function refreshUsersList()
    {
        $this->resetPage();
    }

    public function buttonRoleFilter($query)
    {
        $query->when(in_array($this->filter, ['admin', 'dosen', 'mahasiswa']), function ($q) {
            return $q->whereHas($this->filter);
        });
    }

    private function syncSortField($filter, $sortField)
    {
        if ($filter != '' && $sortField == 'role') {
            $this->sortField = 'name';
        } elseif ($filter != 'mahasiswa' && $sortField == 'tahun_angkatan') {
            $this->sortField = 'name';
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
        $this->inputProdiFilter();
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        $this->syncSortField($this->filter, $this->sortField);

        $queryUser = $this->inputUserSearch();

        $query = clone $queryUser;
        $this->buttonRoleFilter($query);

        if ($this->showDeleted) {
            $query->onlyTrashed();
            $queryUser->onlyTrashed();
        }

        return view('livewire.admin.user-management', [
            'users' => $query->paginate($this->perPage),
            // 'totalUsers' => User::count(),
            // 'totalAdmins' => Admin::count(),
            // 'totalDosens' => Dosen::count(),
            // 'totalMahasiswas' => Mahasiswa::count(),
            'totalUsers' => (clone $queryUser)->count(),
            'totalAdmins' => (clone $queryUser)->whereHas('admin')->count(),
            'totalDosens' => (clone $queryUser)->whereHas('dosen')->count(),
            'totalMahasiswas' => (clone $queryUser)->whereHas('mahasiswa')->count(),
        ]);
    }
}
