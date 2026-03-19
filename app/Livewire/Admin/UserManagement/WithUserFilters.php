<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\User;

// use App\Models\Prodi;
// use App\Models\Jurusan;
// use App\Models\Fakultas;

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

    public function inputMainSearch()
    {
        $query = User::query()->with(['admin', 'dosen', 'mahasiswa']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {

            $roles = ['admin', 'dosen', 'mahasiswa'];

            // field khusus tiap role
            $roleFields = [
                'admin' => ['name', 'nip', 'nitk', 'status'],
                'dosen' => ['name', 'nip', 'nidn', 'nidk', 'status'],
                'mahasiswa' => ['name', 'nim', 'tahun_angkatan', 'status'],
            ];

            $query->where(function ($q) use ($searchTerm, $roles, $roleFields) {
                // email user
                $q->where('users.email', 'like', $searchTerm);

                foreach ($roles as $role) {
                    // field utama role
                    $q->orWhereHas($role, function ($r) use ($searchTerm, $roleFields, $role) {
                        $r->where(function ($sub) use ($searchTerm, $roleFields, $role) {
                            foreach ($roleFields[$role] as $field) {
                                $sub->orWhere($field, 'like', $searchTerm);
                            }

                        });

                    });

                    // prodi
                    $q->orWhereHas("$role.prodi", fn ($r) => $r->where('nama_prodi', 'like', $searchTerm)
                    );
                    // jurusan
                    $q->orWhereHas("$role.prodi.jurusan_rel", fn ($r) => $r->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                    );
                    // fakultas
                    $q->orWhereHas("$role.prodi.jurusan_rel.fakultas_rel", fn ($r) => $r->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm])
                    );
                }

                // 🔹 pencarian role langsung
                $searchLower = strtolower($this->search);
                if (str_contains($searchLower, 'admin')) {
                    $q->orWhereHas('admin');
                }
                if (str_contains($searchLower, 'dosen')) {
                    $q->orWhereHas('dosen');
                }
                if (str_contains($searchLower, 'mahasiswa')) {
                    $q->orWhereHas('mahasiswa');
                }

                // search ID user
                if (is_numeric($this->search)) {
                    $q->orWhere('users.id', $this->search);
                }
            });
        }

        if ($this->selectedProdiId) {
            $query->where(function ($q) {
                $q->whereHas('admin', function ($r) {
                    $r->where('prodi_id', $this->selectedProdiId);
                })
                    ->orWhereHas('dosen', function ($r) {
                        $r->where('prodi_id', $this->selectedProdiId);
                    })
                    ->orWhereHas('mahasiswa', function ($r) {
                        $r->where('prodi_id', $this->selectedProdiId);
                    });
            });
        }

        if ($this->selectedJurusanId) {
            $query->where(function ($q) {
                $q->whereHas('admin.prodi', function ($r) {
                    $r->where('jurusan_id', $this->selectedJurusanId);
                })
                    ->orWhereHas('dosen.prodi', function ($r) {
                        $r->where('jurusan_id', $this->selectedJurusanId);
                    })
                    ->orWhereHas('mahasiswa.prodi', function ($r) {
                        $r->where('jurusan_id', $this->selectedJurusanId);
                    });
            });
        }

        if ($this->selectedFakultasId) {
            $query->where(function ($q) {
                $q->whereHas('admin.prodi.jurusan_rel', function ($r) {
                    $r->where('fakultas_id', $this->selectedFakultasId);
                })
                    ->orWhereHas('dosen.prodi.jurusan_rel', function ($r) {
                        $r->where('fakultas_id', $this->selectedFakultasId);
                    })
                    ->orWhereHas('mahasiswa.prodi.jurusan_rel', function ($r) {
                        $r->where('fakultas_id', $this->selectedFakultasId);
                    });
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

    // public function buttonRoleFilter($query)
    // {
    //     $query->when($this->selectedProdiId, function ($q) {
    //         $q->where(function ($subQ) {
    //             $subQ->whereHas('admin', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
    //                 ->orWhereHas('dosen', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
    //                 ->orWhereHas('mahasiswa', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId));
    //         });
    //     });

    //     $countQueryBase = clone $query;

    //     if ($this->filter === 'admin') {
    //         $query->whereHas('admin');
    //     } elseif ($this->filter === 'dosen') {
    //         $query->whereHas('dosen');
    //     } elseif ($this->filter === 'mahasiswa') {
    //         $query->whereHas('mahasiswa');
    //     }

    //     $totalUsers = $countQueryBase->count();
    //     $totalAdmins = (clone $countQueryBase)->whereHas('admin')->count();
    //     $totalDosens = (clone $countQueryBase)->whereHas('dosen')->count();
    //     $totalMahasiswas = (clone $countQueryBase)->whereHas('mahasiswa')->count();

    //     return [$totalUsers, $totalAdmins, $totalDosens, $totalMahasiswas];
    // }

    // public function buttonRoleFilter($query)
    // {
    //     // Filter berdasarkan Prodi jika dipilih (tetap di query utama)
    //     $query->when($this->selectedProdiId, function ($q) {
    //         $q->where(function ($subQ) {
    //             $subQ->whereHas('admin', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
    //                 ->orWhereHas('dosen', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
    //                 ->orWhereHas('mahasiswa', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId));
    //         });
    //     });

    //     // 1. Ambil Statistik secara terpisah agar tidak bentrok dengan users.*
    //     $stats = User::query()
    //         ->when($this->selectedProdiId, function ($q) {
    //             $q->whereExists(function ($sub) {
    //                 $sub->select(DB::raw(1))
    //                     ->from('admins')
    //                     ->whereColumn('admins.user_id', 'users.id')
    //                     ->where('prodi_id', $this->selectedProdiId);
    //             })->orWhereExists(function ($sub) {
    //                 $sub->select(DB::raw(1))
    //                     ->from('dosens')
    //                     ->whereColumn('dosens.user_id', 'users.id')
    //                     ->where('prodi_id', $this->selectedProdiId);
    //             })->orWhereExists(function ($sub) {
    //                 $sub->select(DB::raw(1))
    //                     ->from('mahasiswas')
    //                     ->whereColumn('mahasiswas.user_id', 'users.id')
    //                     ->where('prodi_id', $this->selectedProdiId);
    //             });
    //         })
    //         ->selectRaw('
    //         COUNT(*) as total,
    //         SUM(EXISTS (SELECT 1 FROM admins WHERE admins.user_id = users.id)) as admins,
    //         SUM(EXISTS (SELECT 1 FROM dosens WHERE dosens.user_id = users.id)) as dosens,
    //         SUM(EXISTS (SELECT 1 FROM mahasiswas WHERE mahasiswas.user_id = users.id)) as mahasiswas
    //     ')->first();

    //     // 2. Terapkan filter role untuk hasil tabel UTAMA
    //     if ($this->filter === 'admin') {
    //         $query->whereHas('admin');
    //     } elseif ($this->filter === 'dosen') {
    //         $query->whereHas('dosen');
    //     } elseif ($this->filter === 'mahasiswa') {
    //         $query->whereHas('mahasiswa');
    //     }

    //     return [
    //         $stats->total ?? 0,
    //         $stats->admins ?? 0,
    //         $stats->dosens ?? 0,
    //         $stats->mahasiswas ?? 0,
    //     ];
    // }

    // public function buttonRoleFilter($query)
    // {
    //     $query->when($this->selectedProdiId, function ($q) {
    //         $q->where(function ($subQ) {
    //             $subQ->whereHas('admin', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
    //                 ->orWhereHas('dosen', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId))
    //                 ->orWhereHas('mahasiswa', fn ($rel) => $rel->where('prodi_id', $this->selectedProdiId));
    //         });
    //     });

    //     $stats = User::query()
    //         ->when($this->selectedProdiId, function ($q) {
    //             $q->where(function ($sub) {
    //                 $sub->whereHas('admin', fn ($r) => $r->where('prodi_id', $this->selectedProdiId))
    //                     ->orWhereHas('dosen', fn ($r) => $r->where('prodi_id', $this->selectedProdiId))
    //                     ->orWhereHas('mahasiswa', fn ($r) => $r->where('prodi_id', $this->selectedProdiId));
    //             });
    //         })
    //         ->selectRaw('
    //         COUNT(*) as total,
    //         SUM(CASE WHEN EXISTS (SELECT 1 FROM admins WHERE admins.user_id = users.id) THEN 1 ELSE 0 END) as admins,
    //         SUM(CASE WHEN EXISTS (SELECT 1 FROM dosens WHERE dosens.user_id = users.id) THEN 1 ELSE 0 END) as dosens,
    //         SUM(CASE WHEN EXISTS (SELECT 1 FROM mahasiswas WHERE mahasiswas.user_id = users.id) THEN 1 ELSE 0 END) as mahasiswas
    //     ')->first();

    //     if ($this->filter === 'admin') {
    //         $query->whereHas('admin');
    //     } elseif ($this->filter === 'dosen') {
    //         $query->whereHas('dosen');
    //     } elseif ($this->filter === 'mahasiswa') {
    //         $query->whereHas('mahasiswa');
    //     }

    //     return [
    //         $stats->total ?? 0,
    //         $stats->admins ?? 0,
    //         $stats->dosens ?? 0,
    //         $stats->mahasiswas ?? 0,
    //     ];
    // }

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
