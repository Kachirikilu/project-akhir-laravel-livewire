<?php

namespace App\Livewire\Admin\UserManagement;

use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Prodi;
use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;

trait WithUserFilters
{
    use WithPagination;

    public $search = '';
    public $filter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public $searchAngkatan = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSearchAngkatan() { $this->resetPage(); }

    public function inputMainSearch()
    {
        $query = User::query()
            ->with(['admin', 'dosen', 'mahasiswa'])
            ->where(function ($q) {
                $q->where('email', 'like', "%{$this->search}%")
                    ->orWhereHas('admin', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen', fn($q) => $q->where('nip', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('nim', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('tahun_angkatan', 'like', "%{$this->search}%"))
                    ->orWhereHas('admin.prodi', fn($q) => $q->where('nama_prodi', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen.prodi', fn($q) => $q->where('nama_prodi', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa.prodi', fn($q) => $q->where('nama_prodi', 'like', "%{$this->search}%"))

                    // 2. Pencarian Nama Fakultas (Masuk ke prodi -> jurusan_rel -> fakultas)
                    ->orWhereHas('admin.prodi.jurusan_rel.fakultas', fn($q) => $q->where('nama_fakultas', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen.prodi.jurusan_rel.fakultas', fn($q) => $q->where('nama_fakultas', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa.prodi.jurusan_rel.fakultas', fn($q) => $q->where('nama_fakultas', 'like', "%{$this->search}%"))

                    // 3. Pencarian Nama Jurusan (Masuk ke prodi -> jurusan_rel)
                    ->orWhereHas('admin.prodi.jurusan_rel', fn($q) => $q->where('nama_jurusan', 'like', "%{$this->search}%"))
                    ->orWhereHas('dosen.prodi.jurusan_rel', fn($q) => $q->where('nama_jurusan', 'like', "%{$this->search}%"))
                    ->orWhereHas('mahasiswa.prodi.jurusan_rel', fn($q) => $q->where('nama_jurusan', 'like', "%{$this->search}%"))

                    ->orWhere('users.id', $this->search);
            });

           $this->sortFieldOrder($query);

            return $query;
    }

    public function filterBy($role)
    {
        $this->filter = $role;
        $this->resetPage();
    }
    public function buttonRoleFilter($query) {
        $query->when($this->selectedProdiId, function ($q) {
            $q->where(function ($subQ) {
                $subQ->whereHas('admin', fn($rel) => $rel->where('prodi_id', $this->selectedProdiId))
                    ->orWhereHas('dosen', fn($rel) => $rel->where('prodi_id', $this->selectedProdiId))
                    ->orWhereHas('mahasiswa', fn($rel) => $rel->where('prodi_id', $this->selectedProdiId));
            });
        });

        $countQueryBase = clone $query;

        if ($this->filter === 'admin') {
            $query->whereHas('admin');
        } elseif ($this->filter === 'dosen') {
            $query->whereHas('dosen');
        } elseif ($this->filter === 'mahasiswa') {
            $query->whereHas('mahasiswa');
        }

        $totalUsers = $countQueryBase->count();
        $totalAdmins = (clone $countQueryBase)->whereHas('admin')->count();
        $totalDosens = (clone $countQueryBase)->whereHas('dosen')->count();
        $totalMahasiswas = (clone $countQueryBase)->whereHas('mahasiswa')->count();

        return [$totalUsers, $totalAdmins, $totalDosens, $totalMahasiswas];
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

    public function sortFieldOrder($query) {
        
        if (!empty($this->searchAngkatan) && $this->filter === 'mahasiswa') {
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
        } elseif ($this->sortField === 'identity') {
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
        } else {
            $field = $this->sortField === 'id' ? 'users.id' : $this->sortField;
            $query->orderBy($field, $this->sortDirection);
        }

        if ($this->filter != '' && $this->sortField == 'role') {
            $this->sortField = 'name';
        } elseif ($this->filter != 'mahasiswa' && $this->sortField == 'tahun_angkatan') {
            $this->sortField = 'name';
        } elseif ($this->filter == 'mahasiswa' && $this->sortField == 'identity2') {
            $this->sortField = 'identity';
        } elseif ($this->filter != 'dosen' && $this->sortField == 'identity3') {
            if ($this->filter == 'mahasiswa') {
                $this->sortField = 'identity';
            } else if ($this->filter == 'dosen') {
                $this->sortField = 'identity2';
            }
        }
        return $query;
    }

}
