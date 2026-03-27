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

    public $filter = '';

    public $sortField = 'name';

    public $sortDirection = 'asc';

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
            $searchTerm = '%'.$this->search.'%';

        $query = User::query()
            ->with(['admin', 'dosen', 'mahasiswa'])
            ->searchUser($searchTerm);

        if ($this->selectedProdiId) {
            $query->inLocationUser('prodi', $this->selectedProdiId);
        }
        
        if ($this->selectedJurusanId) {
            $query->inLocationUser('jurusan', $this->selectedJurusanId);
        }

        if ($this->selectedFakultasId) {
            $query->inLocationUser('fakultas', $this->selectedFakultasId);
        }

        $this->sortFieldOrderUser($query);

        return $query;
    }

    public function filterBy($role)
    {
        $this->filter = $role;
        $this->resetPage();
    }


    public function resetInputFilter()
    {
        $this->reset(['search', 'filter']);
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

    public function sortFieldOrderUser($query)
    {

        if (! empty($this->searchAngkatan) && $this->filter === 'mahasiswa') {
            $query->whereHas('mahasiswa', function ($q) {
                $q->where('tahun_angkatan', 'like', "%{$this->searchAngkatan}%");
            });
        }

        if ($this->sortField === 'role') {
            $query->orderByRaw("
                CASE 
                    WHEN admins.id IS NOT NULL THEN 1
                    WHEN dosens.id IS NOT NULL THEN 2
                    WHEN mahasiswas.id IS NOT NULL THEN 3
                    ELSE 4
                END {$this->sortDirection}
            ")
                ->leftJoin('admins', 'users.id', '=', 'admins.user_id')
                ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
                ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
                ->select('users.*');
        } elseif ($this->sortField === 'name') {
            $query->leftJoin('admins', 'users.id', '=', 'admins.user_id')
                ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
                ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
                ->select('users.*')
                ->orderByRaw("COALESCE(admins.name, dosens.name, mahasiswas.name) {$this->sortDirection}");
        } elseif ($this->sortField === 'identity1') {
            $query->leftjoin('admins', 'users.id', '=', 'admins.user_id')
                ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
                ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
                ->select('users.*')
                ->orderByRaw("COALESCE(admins.nip, dosens.nip, mahasiswas.nim) {$this->sortDirection}");
        } elseif ($this->sortField === 'identity2') {
            $query->leftjoin('admins', 'users.id', '=', 'admins.user_id')
                ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
                ->select('users.*')
                ->orderByRaw("COALESCE(admins.nitk, dosens.nidn) {$this->sortDirection}");
        } elseif ($this->sortField === 'identity3') {
            $query->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
                ->select('users.*')
                ->orderByRaw("COALESCE(dosens.nidk) {$this->sortDirection}");
        } elseif ($this->sortField === 'email') {
            $query->orderBy('users.email', $this->sortDirection);
        } elseif ($this->sortField === 'prodi') {
            $query->leftJoin('admins', 'users.id', '=', 'admins.user_id')
                ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
                ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
                ->leftJoin('prodis as admin_prodis', 'admins.prodi_id', '=', 'admin_prodis.id')
                ->leftJoin('prodis as dosen_prodis', 'dosens.prodi_id', '=', 'dosen_prodis.id')
                ->leftJoin('prodis as mahasiswa_prodis', 'mahasiswas.prodi_id', '=', 'mahasiswa_prodis.id')
                ->select('users.*')
                ->orderByRaw("COALESCE(admin_prodis.nama_prodi, dosen_prodis.nama_prodi, mahasiswa_prodis.nama_prodi) {$this->sortDirection}");
        } elseif ($this->sortField === 'tahun_angkatan') {
            $query->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
                ->select('users.*')
                ->orderBy('mahasiswas.tahun_angkatan', $this->sortDirection);
        } elseif ($this->sortField === 'status') {
            $query->leftJoin('admins', 'users.id', '=', 'admins.user_id')
                ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
                ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
                ->select('users.*')
                ->orderByRaw("COALESCE(admins.status, dosens.status, mahasiswas.status) {$this->sortDirection}");
        } else {
            $field = $this->sortField === 'id' ? 'users.id' : $this->sortField;
            $query->orderBy($field, $this->sortDirection);
        }

        return $query;
    }
}
