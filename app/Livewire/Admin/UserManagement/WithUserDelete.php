<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Global\HasToast;

trait WithUserDelete
{    
    use HasToast;
    public $showUserDelete = false;
    public $userIdToDelete;
    public $userEmailToDelete;
    public $isPermanentDelete = false;

    public function deleteUser($id, $isTrashed = false)
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        $user = $isTrashed ? User::withTrashed()->find($id) : User::find($id);

        if (!$user) {
            $this->toast(type: 'unfound', variant: 'warning', isAKun: 1);
            return;
        }

        if (Auth::id() === $user->id) {
            $this->toast(text: 'Anda tidak dapat menghapus akun sendiri!', variant: 'warning');
            return;
        }

        $this->userIdToDelete = $id;
        $this->userEmailToDelete = $user->email;
        $this->isPermanentDelete = $isTrashed;
        $this->showUserDelete = true;
    }

    public function destroyUser()
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        if (!$this->userIdToDelete) return;

        $type = 'delete';

        try {
            $user = User::withTrashed()->findOrFail($this->userIdToDelete);

            if ($this->isPermanentDelete) {
                $type = 'permanent';
                $user->forceDelete();
            } else {
                $user->delete();
            }

            $this->dispatch('refresh-data-user'); 
            $this->showUserDelete = false;
            $this->toast(message: $this->userEmailToDelete, type: $type, isAkun: true);
            $this->cleanupDeleteStateUser();
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-user');
            $this->showUserDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    public function restoreUser($id)
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            $this->dispatch('refresh-data-user');
            $this->toast(message: $user->email, type: 'recycle', isAkun: true);

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-user');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateUser()
    {
        $this->userIdToDelete = null;
        $this->userEmailToDelete = null;
        $this->isPermanentDelete = false;
        $this->showUserDelete = false;
    }
}