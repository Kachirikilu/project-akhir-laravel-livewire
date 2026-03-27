<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Auth\User;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;

use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithProdiSearchFilters;
use App\Livewire\Admin\UserManagement\WithUserExcel;

class UserManagement extends Component
{
    use WithPagination;
    
    use WithUserModal;
    use WithUserDelete;
    use WithUserFilters;
    use WithProdiSearchFilters;

    use WithUserExcel;

    public $showModal = false;
    public $perPage = 8;
    protected $paginationTheme = 'tailwind';
    protected $listeners = ['refresh-table' => 'refreshUsersList',
    'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        // 'prodi_name' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc']
        
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

    public function render()
    {
        $this->inputProdiFilter(); 
        $query = $this->inputMainSearch();

        $this->buttonRoleFilter($query);

        return view('livewire.admin.user-management', [
            'users' => $query->paginate($this->perPage),
            'totalUsers' => User::count(),
            'totalAdmins' => Admin::count(),
            'totalDosens' => Dosen::count(),
            'totalMahasiswas' => Mahasiswa::count(),
        ]);
    }

}