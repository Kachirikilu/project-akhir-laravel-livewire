<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait WithUserDelete
{    
    public $showDeleteConfirmation = false;
    public $userIdToDelete;
    public $userEmailToDelete;

    public function confirmDelete($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            session()->flash('error', 'Pengguna tidak ditemukan.');
            return;
        }

        if (Auth::id() === $user->id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $this->userIdToDelete = $userId;
        $this->userEmailToDelete = $user->email;
        $this->showDeleteConfirmation = true;
    }

    public function deleteUser()
    {
        if (!$this->userIdToDelete) {
            $this->cancelDelete();
            return;
        }

        try {
            $user = User::findOrFail($this->userIdToDelete);
            $user->admin()->delete();
            $user->dosen()->delete();
            $user->mahasiswa()->delete();
            $user->delete();

            session()->flash('success', 'Pengguna dan data terkait berhasil dihapus.');
            
            $this->reset(['showDeleteConfirmation', 'userIdToDelete', 'userEmailToDelete']);
            $this->resetPage(); 
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pengguna. ' . $e->getMessage());
        }
    }

    public function cancelDelete()
    {
        $this->reset(['showDeleteConfirmation', 'userIdToDelete', 'userEmailToDelete']);
    }
}
