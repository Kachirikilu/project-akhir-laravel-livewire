<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait WithUserDelete
{    
    public $showUserDelete = false;
    public $userIdToDelete;
    public $userEmailToDelete;

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            session()->flash('error', 'Pengguna tidak ditemukan!');
            return;
        }
        if (Auth::id() === $user->id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        $this->userIdToDelete = $id;
        $this->userEmailToDelete = $user->email;

        $this->showUserDelete = true;
        // $this->js("Flux.modal('user-delete').show()");
    }

    public function destroyUser()
    {
        if (!$this->userIdToDelete) return;

        try {
            $user = User::findOrFail($this->userIdToDelete);
            $user->delete();

            $this->js("Flux.toast('Pengguna {$this->userEmailToDelete} berhasil dihapus!')");
            $this->cleanupDeleteState();
            $this->dispatch('refresh-data'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }
            

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal menghapus!' })");
            $this->showUserDelete = false;
        }
    }

    private function cleanupDeleteState()
    {
        $this->userIdToDelete = null;
        $this->userEmailToDelete = null;
        $this->showUserDelete = false;
    }
}
