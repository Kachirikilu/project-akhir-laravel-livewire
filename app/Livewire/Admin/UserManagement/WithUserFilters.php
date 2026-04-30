<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Auth\User;
// use App\Models\ProgramStudi\Prodi;
// use App\Models\ProgramStudi\Departemen;
// use App\Models\ProgramStudi\Fakultas;

use Livewire\WithPagination;

trait WithUserFilters
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $searchAngkatan = '';

    public function updatingSearchAngkatan()
    {
        $this->resetPage();
    }

    public function resetInputAngkatan()
    {
        $this->reset('searchAngkatan');
        $this->resetPage();
    }

    public function inputUserSearch()
    {
        $queryUser = User::query()
            ->with(['admin', 'dosen', 'mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel']);

        $search = $this->search;

        if (! empty($search)) {
            $queryUser->searchUser($search);
        }

        if (! empty($this->searchAngkatan) && $this->switchTable === 'mahasiswa') {
            $queryUser->searchUser($search, true);
        }

        if ($this->selectedPrId) {
            $queryUser->inLocationUser('prodi', $this->selectedPrId);
        }

        if ($this->selectedDpId) {
            $queryUser->inLocationUser('departemen', $this->selectedDpId);
        }

        if ($this->selectedFkId) {
            $queryUser->inLocationUser('fakultas', $this->selectedFkId);
        }

        if ($this->selectedRPSId) {
            $queryUser->whereHas('dosen.rps', function ($q) {
                $q->where('rps.id', $this->selectedRPSId);
            });
        }

        $this->sortFieldOrderUser($queryUser);

        return $queryUser;
    }

    public function buttonRoleFilter($queryUser)
    {
        $queryUser->when(in_array($this->switchTable, ['admin', 'dosen', 'mahasiswa']), function ($q) {
            $q->whereHas($this->switchTable);
        });

        if ($this->switchTable === 'dosen') {
            if (! empty($this->filterDosen)) {
                if ($this->filterDosen == 'dosen-rps') {
                    $queryUser->whereHas('dosen.rps');
                } elseif ($this->filterDosen == 'dosen-non-rps') {
                    $queryUser->whereDoesntHave('dosen.rps');
                }
            }
        }

        // Filter by status
        if ($this->filterStatus === 'aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin', fn ($sub) => $sub->where('status', 'Aktif'))
                    ->orWhereHas('dosen', fn ($sub) => $sub->where('status', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'non-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin', fn ($sub) => $sub->where('status', '!=', 'Aktif'))
                    ->orWhereHas('dosen', fn ($sub) => $sub->where('status', '!=', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', '!=', 'Aktif'));
            });
        }

        return $queryUser;
    }

    public function filterByUser($role)
    {
        $this->switchTable = $role;
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function sortFieldOrderUser($queryUser)
    {
        $profileFields = ['role', 'name', 'identity1', 'identity2', 'identity3', 'prodi', 'status', 'angkatan'];

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
            'role' => 'CASE 
                        WHEN admins.id IS NOT NULL THEN 1
                        WHEN dosens.id IS NOT NULL THEN 2
                        WHEN mahasiswas.id IS NOT NULL THEN 3
                        ELSE 4
                    END',

            'name' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
            'identity1' => 'COALESCE(admins.nip, dosens.nip, mahasiswas.nim)',
            'identity2' => 'COALESCE(admins.nitk, dosens.nidn)',
            'identity3' => 'dosens.nidk',
            'status' => 'COALESCE(admins.status, dosens.status, mahasiswas.status)',

            'prodi' => $this->joinProdiAndGetSortSql($queryUser),

            'angkatan' => 'mahasiswas.angkatan',
            'created_at' => 'users.created_at',
            'updated_at' => 'users.updated_at',

            default => 'users.id'
        };

        return $queryUser->orderByRaw("$orderByRaw {$this->sortDirection}");
    }

    private function joinProdiAndGetSortSql($queryUser)
    {
        $queryUser->leftJoin('prodis as admin_prodis', 'admins.pr_id', '=', 'admin_prodis.id')
            ->leftJoin('prodis as dosen_prodis', 'dosens.pr_id', '=', 'dosen_prodis.id')
            ->leftJoin('prodis as mahasiswa_prodis', 'mahasiswas.pr_id', '=', 'mahasiswa_prodis.id');

        return 'COALESCE(admin_prodis.nama_pr, dosen_prodis.nama_pr, mahasiswa_prodis.nama_pr)';
    }
}
