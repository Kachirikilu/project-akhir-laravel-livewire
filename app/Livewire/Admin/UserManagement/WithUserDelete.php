<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait WithUserDelete
{    
    public $showUserDelete = false;
    public $userIdToDelete;
    public $userEmailToDelete;
    public $isPermanentDelete = false;

    public function deleteUser($id, $isTrashed = false)
    {
        $user = $isTrashed ? User::withTrashed()->find($id) : User::find($id);

        if (!$user) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Pengguna tidak ditemukan!' })");
            return;
        }

        if (Auth::id() === $user->id) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Anda tidak dapat menghapus akun sendiri!' })");
            return;
        }

        $this->userIdToDelete = $id;
        $this->userEmailToDelete = $user->email;
        $this->isPermanentDelete = $isTrashed;
        $this->showUserDelete = true;
    }

    public function destroyUser()
    {
        if (!$this->userIdToDelete) return;

        try {
            $user = User::withTrashed()->findOrFail($this->userIdToDelete);

            if ($this->isPermanentDelete) {
                $user->forceDelete();
                $message = "Pengguna {$this->userEmailToDelete} BERHASIL DIHAPUS PERMANEN!";
            } else {
                $user->delete();
                $message = "Pengguna {$this->userEmailToDelete} berhasil dipindahkan ke sampah!";
            }

            $this->js("Flux.toast('{$message}')");
            $this->cleanupDeleteState();
            $this->dispatch('refresh-data'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal memproses permintaan!' })");
        }
    }

    public function restoreUser($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            $this->js("Flux.toast('Akses pengguna {$user->email} berhasil dipulihkan!')");
            $this->dispatch('refresh-data');

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal memulihkan pengguna!' })");
        }
    }

    private function cleanupDeleteState()
    {
        $this->userIdToDelete = null;
        $this->userEmailToDelete = null;
        $this->isPermanentDelete = false;
        $this->showUserDelete = false;
    }
}