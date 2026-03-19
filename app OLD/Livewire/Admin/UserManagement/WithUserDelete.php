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
        $this->js("Flux.modal('delete-confirmation').show()");
    }

    public function deleteUser()
    {
        if (!$this->userIdToDelete) return;

        try {
            $user = User::findOrFail($this->userIdToDelete);
            $user->delete();

            $this->js("Flux.toast('Pengguna berhasil dihapus')");
            
            $this->userIdToDelete = null;
            $this->userEmailToDelete = null;

            $this->resetPage();
            $this->showDeleteConfirmation = false;

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal menghapus' })");
            $this->showDeleteConfirmation = false;
        }
    }
}
