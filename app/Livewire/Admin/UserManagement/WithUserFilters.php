<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Auth\User;

// use App\Models\ProgramStudi\Prodi;
// use App\Models\ProgramStudi\Jurusan;
// use App\Models\ProgramStudi\Fakultas;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

trait WithUserFilters
{
    use WithPagination;

    public $search = '';

    public $filterUser = '';

    public $searchAngkatan = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSearchAngkatan()
    {
        $this->resetPage();
    }

    public function inputUserSearch()
    {
        $queryUser = User::query()
            ->with(['admin', 'dosen', 'mahasiswa', 'mahasiswa.prodi', 'mahasiswa.prodi.jurusan_rel', 'mahasiswa.prodi.jurusan_rel.fakultas_rel']);

        $search = $this->search;

        if (! empty($search)) {
            $queryUser->searchUser($search);
        }
            
        if ($this->selectedProdiId) {
            $queryUser->inLocationUser('prodi', $this->selectedProdiId);
        }
        
        if ($this->selectedJurusanId) {
            $queryUser->inLocationUser('jurusan', $this->selectedJurusanId);
        }

        if ($this->selectedFakultasId) {
            $queryUser->inLocationUser('fakultas', $this->selectedFakultasId);
        }

        $this->sortFieldOrderUser($queryUser);

        return $queryUser;
    }

    public function filterByUser($role)
    {
        $this->filterUser = $role;
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function resetInputAngkatan()
    {
        $this->reset('searchAngkatan');
        $this->resetPage();
    }

    public function sortFieldOrderUser($queryUser)
    {
        if (!empty($this->searchAngkatan) && $this->filterUser === 'mahasiswa') {
            $queryUser->whereHas('mahasiswa', fn($q) => 
                $q->where('tahun_angkatan', 'like', "%{$this->searchAngkatan}%")
            );
        }

        $profileFields = ['role', 'name', 'identity1', 'identity2', 'identity3', 'prodi', 'status', 'tahun_angkatan'];

        if (in_array($this->sortField, $profileFields)) {
            return $this->applyUserCombinedSort($queryUser);
        }

        $field = ($this->sortField === 'id') ? 'users.id' : $this->sortField;
        return $queryUser->orderBy($field, $this->sortDirection);
    }

    private function applyUserCombinedSort($queryUser)
    {
        $queryUser->leftJoin('admins', 'users.id', '=', 'admins.user_id')
            ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
            ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
            ->select('users.*');

        $orderByRaw = match ($this->sortField) {
            'role' => "CASE 
                        WHEN admins.id IS NOT NULL THEN 1
                        WHEN dosens.id IS NOT NULL THEN 2
                        WHEN mahasiswas.id IS NOT NULL THEN 3
                        ELSE 4
                    END",
            
            'name'      => "COALESCE(admins.name, dosens.name, mahasiswas.name)",
            'identity1' => "COALESCE(admins.nip, dosens.nip, mahasiswas.nim)",
            'identity2' => "COALESCE(admins.nitk, dosens.nidn)",
            'identity3' => "dosens.nidk",
            'status'    => "COALESCE(admins.status, dosens.status, mahasiswas.status)",
            
            'prodi'     => $this->joinProdiAndGetSortSql($queryUser),
            
            'tahun_angkatan' => "mahasiswas.tahun_angkatan",
            'created_at' => "users.created_at",
            'updated_at' => "users.updated_at",
            
            default => "users.id"
        };

        return $queryUser->orderByRaw("$orderByRaw {$this->sortDirection}");
    }

    private function joinProdiAndGetSortSql($queryUser)
    {
        $queryUser->leftJoin('prodis as admin_prodis', 'admins.prodi_id', '=', 'admin_prodis.id')
            ->leftJoin('prodis as dosen_prodis', 'dosens.prodi_id', '=', 'dosen_prodis.id')
            ->leftJoin('prodis as mahasiswa_prodis', 'mahasiswas.prodi_id', '=', 'mahasiswa_prodis.id');

        return "COALESCE(admin_prodis.nama_prodi, dosen_prodis.nama_prodi, mahasiswa_prodis.nama_prodi)";
    }
}
