<?php

namespace App\Livewire\Admin\UserManagement;use App\Models\Prodi;
use App\Models\User;
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

    public function inputMainSearch()
    {
        $query = User::query()->with(['admin', 'dosen', 'mahasiswa']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('email', 'like', $searchTerm)
                    ->orWhereHas('admin', fn ($q) => $q->where('name', 'like', $searchTerm))
                    ->orWhereHas('dosen', fn ($q) => $q->where('name', 'like', $searchTerm))
                    ->orWhereHas('dosen', fn ($q) => $q->where('nip', 'like', $searchTerm))
                    ->orWhereHas('mahasiswa', fn ($q) => $q->where('name', 'like', $searchTerm))
                    ->orWhereHas('mahasiswa', fn ($q) => $q->where('nim', 'like', $searchTerm))
                    ->orWhereHas('mahasiswa', fn ($q) => $q->where('tahun_angkatan', 'like', $searchTerm))
                    ->orWhereHas('admin.prodi', fn ($q) => $q->where('nama_prodi', 'like', $searchTerm))
                    ->orWhereHas('dosen.prodi', fn ($q) => $q->where('nama_prodi', 'like', $searchTerm))
                    ->orWhereHas('mahasiswa.prodi', fn ($q) => $q->where('nama_prodi', 'like', $searchTerm))

                    // 2. Pencarian Nama Fakultas (Masuk ke prodi -> jurusan_rel -> fakultas)
                    ->orWhereHas('admin.prodi.jurusan_rel.fakultas_rel', fn ($q) => $q->where('nama_fakultas', 'like', $searchTerm))
                    ->orWhereHas('dosen.prodi.jurusan_rel.fakultas_rel', fn ($q) => $q->where('nama_fakultas', 'like', $searchTerm))
                    ->orWhereHas('mahasiswa.prodi.jurusan_rel.fakultas_rel', fn ($q) => $q->where('nama_fakultas', 'like', $searchTerm))

                    // 3. Pencarian Nama Jurusan (Masuk ke prodi -> jurusan_rel)
                    ->orWhereHas('admin.prodi.jurusan_rel', fn ($q) => $q->where('nama_jurusan', 'like', $searchTerm))
                    ->orWhereHas('dosen.prodi.jurusan_rel', fn ($q) => $q->where('nama_jurusan', 'like', $searchTerm))
                    ->orWhereHas('mahasiswa.prodi.jurusan_rel', fn ($q) => $q->where('nama_jurusan', 'like', $searchTerm))

                    ->orWhere('users.id', $this->search);
            });
        }

        $this->sortFieldOrder($query);

        return $query;
    }

    public function filterBy($role)
    {
        $this->filter = $role;
        $this->resetPage();
    }

    public function buttonRoleFilter($query)
    {
        $query->when($this->selectedProdiId, function ($q) {
            $q->where(function ($subQ) {
                $subQ->whereHas('admin', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
                    ->orWhereHas('dosen', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
                    ->orWhereHas('mahasiswa', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId));
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

    public function sortFieldOrder($query)
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
        } else {
            $field = $this->sortField === 'id' ? 'users.id' : $this->sortField;
            $query->orderBy($field, $this->sortDirection);
        }

        if ($this->filter != '' && $this->sortField == 'role') {
            $this->sortField = 'name';
        } elseif ($this->filter != 'mahasiswa' && $this->sortField == 'tahun_angkatan') {
            $this->sortField = 'name';
        } elseif ($this->filter == 'mahasiswa' && $this->sortField == 'identity2') {
            $this->sortField = 'identity1';
        } elseif ($this->filter != 'dosen' && $this->sortField == 'identity3') {
            if ($this->filter == 'mahasiswa') {
                $this->sortField = 'identity1';
            } elseif ($this->filter == 'dosen') {
                $this->sortField = 'identity2';
            }
        }

        return $query;
    }
}
