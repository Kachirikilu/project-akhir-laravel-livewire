<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\GlobalManagement\WithProdiSearchFilters;
use App\Livewire\Admin\GlobalManagement\WithJurusanSearchFilters;
use App\Livewire\Admin\GlobalManagement\WithFakultasSearchFilters;

use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithJurusanFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;

use App\Livewire\Admin\UserManagement\WithUserExcel;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Admin\UserManagement\WithUserDelete;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;

use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithProdiSearchFilters;
    use WithJurusanSearchFilters;
    use WithFakultasSearchFilters;

    use WithUserFilters;
    // use WithProdiFilters;
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

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],

        'selectedProdiId' => ['except' => null],
        'selectedJurusanId' => ['except' => null],
        'selectedFakultasId' => ['except' => null]

        // 'selectedProdiName' => ['except' => ''],
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
        // 'prodi_name_search' => ['except' => ''],
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
            $sortField = 'name';
        } elseif ($filter != 'mahasiswa' && $sortField == 'tahun_angkatan') {
            $sortField = 'name';
        } elseif ($filter == 'mahasiswa' && $sortField == 'identity2') {
            $sortField = 'identity1';
        } elseif ($filter != 'dosen' && $sortField == 'identity3') {
            if ($filter == 'mahasiswa') {
                $sortField = 'identity1';
            } elseif ($filter == 'dosen') {
                $sortField = 'identity2';
            }
        }
    }

    public function render()
    {
        $this->inputProdiFilter();
        $this->inputJurusanFilter();
        $this->inputFakultasFilter();

        $this->syncSortField($this->filter, $this->sortField);

        $baseQuery = $this->inputMainSearch();

        $query = clone $baseQuery;
        $this->buttonRoleFilter($query);

        return view('livewire.admin.user-management', [
            'users' => $query->paginate($this->perPage),
            // 'totalUsers' => User::count(),
            // 'totalAdmins' => Admin::count(),
            // 'totalDosens' => Dosen::count(),
            // 'totalMahasiswas' => Mahasiswa::count(),
            'totalUsers' => (clone $baseQuery)->count(),
            'totalAdmins' => (clone $baseQuery)->whereHas('admin')->count(),
            'totalDosens' => (clone $baseQuery)->whereHas('dosen')->count(),
            'totalMahasiswas' => (clone $baseQuery)->whereHas('mahasiswa')->count(),
        ]);
    }
}
