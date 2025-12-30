<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

use App\Models\User;

trait WithUserQuery
{
    public $perPage = 5;
    public $search = '';
    public $filter = '';

    protected $updatesQueryString = ['search', 'filter'];

    public function renderUserManagement()
    {
        // BASE QUERY (untuk search)
        $baseQuery = User::query()
            ->with(['admin', 'dosen', 'mahasiswa'])
            ->where(function ($q) {
                $q->where('email', 'like', "%{$this->search}%")
                    ->orWhereHas('admin', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            });

        // CLONE UNTUK COUNT
        $countQuery = clone $baseQuery;

        // FILTER ROLE (untuk tabel)
        if ($this->filter === 'admin') {
            $baseQuery->whereHas('admin');
        } elseif ($this->filter === 'dosen') {
            $baseQuery->whereHas('dosen');
        } elseif ($this->filter === 'mahasiswa') {
            $baseQuery->whereHas('mahasiswa');
        }

        return view('livewire.admin.user-management', [
            'users' => $baseQuery->paginate($this->perPage),
            'totalUsers'       => $countQuery->count(),
            'totalAdmins'      => (clone $countQuery)->whereHas('admin')->count(),
            'totalDosens'      => (clone $countQuery)->whereHas('dosen')->count(),
            'totalMahasiswas'  => (clone $countQuery)->whereHas('mahasiswa')->count(),
        ]);
    }
}
