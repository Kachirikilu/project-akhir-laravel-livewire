<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithProdiFilters;
use App\Livewire\Admin\UserManagement\WithUserExcel;

class UserManagement extends Component
{
    use WithPagination;
    
    use WithUserModal;
    use WithUserDelete;
    use WithUserFilters;
    use WithProdiFilters;

    use WithUserExcel;

    public $perPage = 8;
    // public $showPerPage = false;

    protected $paginationTheme = 'tailwind';
    protected $listeners = ['refresh-table' => 'refreshUsersList',
    'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'selectedProdiName' => ['except' => ''],
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

    public function render()
    {
        $query = $this->inputMainSearch();
        $countTotal = $this->buttonRoleFilter($query);
        
        $this->inputProdiFilter();

        // if ($this->perPage == 0) {
        //     $this->showPerPage = true;
        // }

        return view('livewire.admin.user-management', [
            'users' => $query->paginate($this->perPage),
            'totalUsers' => $countTotal[0],
            'totalAdmins' => $countTotal[1],
            'totalDosens' => $countTotal[2],
            'totalMahasiswas' => $countTotal[3],
        ]);
    }

}