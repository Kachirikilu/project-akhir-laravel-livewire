<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait WithUserDelete
{
    public $showUserDelete = false;
    public $userIdToDelete;
    public $userEmailToDelete;

    public function confirmDelete($id)
    {
        if (Auth::id() === $id) return;

        $user = User::find($id);
        if (!$user) return;

        $this->userIdToDelete = $id;
        $this->userEmailToDelete = $user->email;
        $this->showUserDelete = true;
    }

    public function deleteUser()
    {
        User::findOrFail($this->userIdToDelete)->delete();
        $this->reset(['showUserDelete', 'userIdToDelete']);
        $this->resetPage();
    }
}
