<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;

trait WithUserCrud
{
    public $showModal = false;
    public $isEditing = false;
    public $roleType;

    public $userId, $email, $password, $name, $nip, $nim, $tahun_angkatan;

    public function showAddModal($role)
    {
        $this->resetInput();
        $this->roleType = $role;
        $this->showModal = true;
    }

    public function resetInput()
    {
        $this->reset([
            'userId', 'email', 'password', 'name',
            'nip', 'nim', 'tahun_angkatan',
            'prodi_id', 'prodi_name_search'
        ]);
    }
}
