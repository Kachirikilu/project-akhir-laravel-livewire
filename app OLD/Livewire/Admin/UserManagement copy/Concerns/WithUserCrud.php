<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

use App\Models\Auth\User;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
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
            'prodi_id', 'prodiNameSearch'
        ]);
    }
}
