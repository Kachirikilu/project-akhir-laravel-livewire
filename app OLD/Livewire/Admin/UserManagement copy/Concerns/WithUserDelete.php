<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait WithUserDelete
{
    public $showDeleteConfirmation = false;
    public $userIdToDelete;
    public $userEmailToDelete;

    public function confirmDelete($id)
    {
        if (Auth::id() === $id) return;

        $user = User::find($id);
        if (!$user) return;

        $this->userIdToDelete = $id;
        $this->userEmailToDelete = $user->email;
        $this->showDeleteConfirmation = true;
    }

    public function deleteUser()
    {
        User::findOrFail($this->userIdToDelete)->delete();
        $this->reset(['showDeleteConfirmation', 'userIdToDelete']);
        $this->resetPage();
    }
}
