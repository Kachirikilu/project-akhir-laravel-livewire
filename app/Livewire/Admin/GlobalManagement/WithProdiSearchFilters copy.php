<?php

namespace App\Livewire\Admin\GlobalManagement;

use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithProdiSearchFilters
{
    use WithPagination;

    public $prodiSearchQuery = '';

    public $prodiSearchResults = [];

    public $selectedProdiName = '';

    public $prodi_id;

    public $selected_kode_pr;

    public $prodi_name_search = '';

    public $prodi_results = [];

    public $selectedProdiId = null;

    // Punya WithMatkulModal
    public $mkType = '';
    public $showMKModal = false;

    // public function inputProdiFilter()
    // {
    //     $searchTerm = '%'.$this->prodiSearchQuery.'%';

    //     if ((strlen($this->prodiSearchQuery) > 1 || is_numeric($this->prodiSearchQuery)) && ! $this->selectedProdiName) {
    //         $this->prodiSearchResults = Prodi::with(['jurusan_rel.fakultas_rel'])
    //             ->where('nama_prodi', 'like', $searchTerm)
    //             ->orWhere('id', $this->prodiSearchQuery)
    //             ->orWhereHas('jurusan_rel', function ($q) use ($searchTerm) {
    //                 $q->where('nama_jurusan', 'like', $searchTerm)
    //                     ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
    //                     ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
    //                         $sq->where('nama_fakultas', 'like', $searchTerm)
    //                             ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
    //                     });
    //             })
    //             ->limit(12)
    //             ->get()
    //             ->map(fn ($p) => [
    //                 'id' => $p->id,
    //                 'kode' => $p->kode_pr ?? $p->jurusan_rel->kode_jr ?? $p->fakultas_rel->kode_fk ?? 'UNI',
    //                 'prodi' => $p->prodi,
    //                 'jurusan' => $p->jurusan,
    //                 'fakultas' => $p->fakultas,
    //             ])->toArray();
    //     } elseif (empty($this->prodiSearchQuery) || $this->selectedProdiName) {
    //         $this->prodiSearchResults = $this->getProdibyUser();
    //     } else {
    //         $this->prodiSearchResults = [];
    //     }
    // }

    public function inputProdiFilter()
    {
        $searchTerm = '%'.$this->prodiSearchQuery.'%';

        if ((strlen($this->prodiSearchQuery) > 1 || is_numeric($this->prodiSearchQuery)) && ! $this->selectedProdiName) {
            $this->prodiSearchResults = Prodi::with(['jurusan_rel.fakultas_rel'])
                ->where(function ($query) use ($searchTerm) {
                    $query->where('nama_prodi', 'like', $searchTerm)
                        ->orWhere('kode_pr', 'like', $searchTerm) // Cari berdasarkan kode prodi
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhereHas('jurusan_rel', function ($q) use ($searchTerm) {
                            $q->where('nama_jurusan', 'like', $searchTerm)
                                ->orWhere('kode_jr', 'like', $searchTerm) // Cari berdasarkan kode jurusan
                                ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                                ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
                                    $sq->where('nama_fakultas', 'like', $searchTerm)
                                        ->orWhere('kode_fk', 'like', $searchTerm) // Cari berdasarkan kode fakultas
                                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                                });
                        });
                })
                ->limit(12)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'kode' => $p->kode_pr
                        ?? $p->jurusan_rel?->kode_jr
                        ?? $p->jurusan_rel?->fakultas_rel?->kode_fk
                        ?? 'UNI',
                    'prodi' => $p->nama_prodi,
                    'jurusan' => $p->jurusan_rel?->nama_jurusan,
                    'fakultas' => $p->jurusan_rel?->fakultas_rel?->nama_fakultas,
                ])->toArray();
        } elseif (empty($this->prodiSearchQuery) || $this->selectedProdiName) {
            $this->prodiSearchResults = $this->getProdibyUser();
        } else {
            $this->prodiSearchResults = [];
        }
    }

    public function resetProdiFilter()
    {
        $this->reset(['selectedProdiId', 'selectedProdiName', 'prodiSearchQuery', 'selected_kode_pr']);
        $this->resetPage();
    }

    // public function selectProdiForFilter($prodiId)
    // {
    //     $prodi = Prodi::find($prodiId);
    //     if ($prodi) {
    //         $this->selectedProdiId = $prodiId;
    //         $this->selectedProdiName = $prodi->nama_prodi;
    //         $this->prodiSearchQuery = '';
    //         $this->resetPage();
    //     }
    // }

    public function selectProdiForFilter($id)
    {
        $data = Prodi::with(['jurusan_rel.fakultas_rel'])->find($id);

        if ($data) {
            $this->selectedProdiId = $id;

            $this->selected_kode_pr = $data->kode_pr
                ?? $data->jurusan_rel?->kode_jr
                ?? $data->jurusan_rel?->fakultas_rel?->kode_fk
                ?? 'UNI';

            $this->selectedProdiName = $data->nama_prodi;
            $this->prodiSearchQuery = $data->nama_prodi;
            $this->prodiSearchResults = [];
            $this->resetPage();
        }
    }

    // public function updatedProdiNameSearch($value)
    // {
    //     $this->prodi_id = null;
    //     $this->selected_kode_pr = null;
    //     $this->resetErrorBag(['prodi_id', 'prodi_name_search']);

    //     if (strlen($value) > 0) {
    //         $searchTerm = '%'.$value.'%';

    //         $results = Prodi::query()
    //             ->with(['jurusan_rel.fakultas_rel'])
    //             ->where(function ($q) use ($searchTerm) {
    //                 $q->where('nama_prodi', 'like', $searchTerm)
    //                     ->orWhere('id', 'like', $searchTerm)
    //                     ->orWhereHas('jurusan_rel', function ($sq) use ($searchTerm) {
    //                         $sq->where('nama_jurusan', 'like', $searchTerm)
    //                             ->orWhereHas('fakultas_rel', function ($ssq) use ($searchTerm) {
    //                                 $ssq->where('nama_fakultas', 'like', $searchTerm);
    //                             });
    //                     });
    //             })
    //             ->limit(12)
    //             ->get();

    //         $this->prodi_results = $results->map(function ($prodi) {
    //             return [
    //                 'id' => $prodi->id,
    //                 'kode' => $prodi->kode_pr
    //                     ?? $prodi->jurusan_rel?->kode_jr
    //                     ?? $prodi->jurusan_rel?->fakultas_rel?->kode_fk
    //                     ?? 'UNI',
    //                 'prodi' => $prodi->nama_prodi,
    //                 'jurusan' => $prodi->jurusan_rel?->nama_jurusan,
    //                 'fakultas' => $prodi->jurusan_rel?->fakultas_rel?->nama_fakultas,
    //             ];
    //         })->toArray();

    //         $exactMatch = $results->first(function ($prodi) use ($value) {
    //             return str($value)->lower()->trim()->is(str($prodi->nama_prodi)->lower());
    //         });

    //         if ($exactMatch) {
    //             $this->prodi_id = $exactMatch->id;
    //             $this->selected_kode_pr = $exactMatch->kode_pr
    //                 ?? $exactMatch->jurusan_rel?->kode_jr
    //                 ?? $exactMatch->jurusan_rel?->fakultas_rel?->kode_fk
    //                 ?? 'UNI';
    //             $this->prodi_name_search = $exactMatch->nama_prodi;
    //             $this->prodi_results = [];
    //         }

    //     } else {
    //         if (Auth::user()->admin?->prodi_id) {
    //             $this->prodi_results = $this->getProdibyUser();
    //         } else {
    //             $this->prodi_results = Prodi::with(['jurusan_rel.fakultas_rel'])
    //                 ->orderBy('nama_prodi')
    //                 ->limit(12)
    //                 ->get()
    //                 ->map(fn ($p) => [
    //                     'id' => $p->id,
    //                     'kode' => $p->kode_pr
    //                         ?? $p->jurusan_rel?->kode_jr
    //                         ?? $p->jurusan_rel?->fakultas_rel?->kode_fk
    //                         ?? 'UNI',
    //                     'prodi' => $p->nama_prodi,
    //                     'jurusan' => $p->jurusan_rel?->nama_jurusan,
    //                     'fakultas' => $p->jurusan_rel?->fakultas_rel?->nama_fakultas,
    //                 ])->toArray();
    //         }
    //     }
    // }

    // public function updatedProdiNameSearch($value)
    // {
    //     $this->prodi_id = null;
    //     $this->selected_kode_pr = null;
    //     $this->resetErrorBag(['prodi_id', 'prodi_name_search']);

    //     if (strlen($value) > 0) {
    //         $searchTerm = '%'.$value.'%';

    //         $results = Prodi::query()
    //             ->with(['jurusan_rel.fakultas_rel'])
    //             ->where(function ($q) use ($searchTerm) {
    //                 $q->where('nama_prodi', 'like', $searchTerm)
    //                     ->orWhere('kode_pr', 'like', $searchTerm) // 🔹 Cari kode prodi
    //                     ->orWhere('id', 'like', $searchTerm)
    //                     ->orWhereHas('jurusan_rel', function ($sq) use ($searchTerm) {
    //                         $sq->where('nama_jurusan', 'like', $searchTerm)
    //                             ->orWhere('kode_jr', 'like', $searchTerm) // 🔹 Cari kode jurusan
    //                             ->orWhereHas('fakultas_rel', function ($ssq) use ($searchTerm) {
    //                                 $ssq->where('nama_fakultas', 'like', $searchTerm)
    //                                     ->orWhere('kode_fk', 'like', $searchTerm); // 🔹 Cari kode fakultas
    //                             });
    //                     });
    //             })
    //             ->limit(12)
    //             ->get();

    //         $this->prodi_results = $results->map(function ($prodi) {
    //             return [
    //                 'id' => $prodi->id,
    //                 'kode' => $prodi->kode_pr
    //                     ?? $prodi->jurusan_rel?->kode_jr
    //                     ?? $prodi->jurusan_rel?->fakultas_rel?->kode_fk
    //                     ?? 'UNI',
    //                 'prodi' => $prodi->nama_prodi,
    //                 'jurusan' => $prodi->jurusan_rel?->nama_jurusan,
    //                 'fakultas' => $prodi->jurusan_rel?->fakultas_rel?->nama_fakultas,
    //             ];
    //         })->toArray();

    //         // 🔹 Exact Match sekarang mendukung Nama dan Kode Prodi
    //         $exactMatch = $results->first(function ($prodi) use ($value) {
    //             $input = str($value)->lower()->trim();
    //             $nama = str($prodi->nama_prodi)->lower();
    //             $kode = str($prodi->kode_pr)->lower();

    //             return $input->is([$nama, $kode]);
    //         });

    //         if ($exactMatch) {
    //             $this->prodi_id = $exactMatch->id;
    //             $this->selected_kode_pr = $exactMatch->kode_pr
    //                 ?? $exactMatch->jurusan_rel?->kode_jr
    //                 ?? $exactMatch->jurusan_rel?->fakultas_rel?->kode_fk
    //                 ?? 'UNI';
    //             $this->prodi_name_search = $exactMatch->nama_prodi;
    //             $this->prodi_results = [];
    //         }

    //     } else {
    //         if (Auth::user()->admin?->prodi_id) {
    //             $this->prodi_results = $this->getProdibyUser();
    //         } else {
    //             $this->prodi_results = Prodi::with(['jurusan_rel.fakultas_rel'])
    //                 ->orderBy('nama_prodi')
    //                 ->limit(12)
    //                 ->get()
    //                 ->map(fn ($p) => [
    //                     'id' => $p->id,
    //                     'kode' => $p->kode_pr
    //                         ?? $p->jurusan_rel?->kode_jr
    //                         ?? $p->jurusan_rel?->fakultas_rel?->kode_fk
    //                         ?? 'UNI',
    //                     'prodi' => $p->nama_prodi,
    //                     'jurusan' => $p->jurusan_rel?->nama_jurusan,
    //                     'fakultas' => $p->jurusan_rel?->fakultas_rel?->nama_fakultas,
    //                 ])->toArray();
    //         }
    //     }
    // }

    public function updatedProdiNameSearch($value)
    {
        // 1. Reset State Awal
        $this->prodi_id = null;
        $this->selected_kode_pr = null;
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);

        // 2. Inisialisasi Query Dasar dengan Relasi
        $query = Prodi::query()->with(['jurusan_rel.fakultas_rel']);

        // 3. Filter Berdasarkan Mode Mata Kuliah (Scope Constraints)
        if ($this->mkType === 'mk-jurusan' && filled($this->jurusan_id) && $this->showMKModal) {
            $query->where('jurusan_id', $this->jurusan_id);
        } elseif ($this->mkType === 'mk-fakultas' && filled($this->fakultas_id) && $this->showMKModal) {
            $query->whereHas('jurusan_rel', function ($q) {
                $q->where('fakultas_id', $this->fakultas_id);
            });
        }

        // 4. Logika Pencarian (Jika User Mengetik Sesuatu)
        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_prodi', 'like', $searchTerm)
                    ->orWhere('kode_pr', 'like', $searchTerm)
                    ->orWhere('id', 'like', $searchTerm)
                    ->orWhereHas('jurusan_rel', function ($sq) use ($searchTerm) {
                        $sq->where('nama_jurusan', 'like', $searchTerm)
                            ->orWhere('kode_jr', 'like', $searchTerm)
                            ->orWhereHas('fakultas_rel', function ($ssq) use ($searchTerm) {
                                $ssq->where('nama_fakultas', 'like', $searchTerm)
                                    ->orWhere('kode_fk', 'like', $searchTerm);
                            });
                    });
            })
                ->limit(12)
                ->get();

            // Mapping Hasil Pencarian untuk Alpine/Dropdown
            $this->prodi_results = $results->map(function ($prodi) {
                return [
                    'id' => $prodi->id,
                    'kode' => $prodi->kode_pr
                        ?? $prodi->jurusan_rel?->kode_jr
                        ?? $prodi->jurusan_rel?->fakultas_rel?->kode_fk
                        ?? 'UNI',
                    'prodi' => $prodi->nama_prodi,
                    'jurusan' => $prodi->jurusan_rel?->nama_jurusan,
                    'fakultas' => $prodi->jurusan_rel?->fakultas_rel?->nama_fakultas,
                ];
            })->toArray();

            // Cek Exact Match (Jika input user persis sama dengan Nama atau Kode)
            $exactMatch = $results->first(function ($prodi) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($prodi->nama_prodi)->lower();
                $kode = str($prodi->kode_pr)->lower();

                return $input->is([$nama, $kode]);
            });

            if ($exactMatch) {
                $this->prodi_id = $exactMatch->id;
                $this->selected_kode_pr = $exactMatch->kode_pr
                    ?? $exactMatch->jurusan_rel?->kode_jr ?? $exactMatch->jurusan_rel?->fakultas_rel?->kode_fk
                    ?? 'UNI';
                $this->prodi_name_search = $exactMatch->nama_prodi;
                $this->prodi_results = [];
            }
        }
        // 5. Default State (Jika input kosong / pertama kali klik)
        else {
            $this->prodi_results = $query->orderBy('nama_prodi')
                ->limit(12)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'kode' => $p->kode_pr ?? $p->jurusan_rel?->kode_jr ?? $p->jurusan_rel?->fakultas_rel?->kode_fk ?? 'UNI',
                    'prodi' => $p->nama_prodi,
                    'jurusan' => $p->jurusan_rel?->nama_jurusan,
                    'fakultas' => $p->jurusan_rel?->fakultas_rel?->nama_fakultas,
                ])->toArray();
        }
    }

    public function getProdibyUser()
    {
        $admin = Auth::user()?->admin;
        $userProdi = $admin ? $admin->prodi()->first() : null;

        if (! $userProdi) {
            return [];
        }

        $namaProdiUser = $userProdi->nama_prodi;
        $jurusanIdUser = $userProdi->jurusan_id;
        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id;

        $mainResults = Prodi::query()
            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
            ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
            ->where('jurusans.fakultas_id', $fakultasIdUser)
            ->orderByRaw('
            CASE
                WHEN prodis.nama_prodi = ? THEN 0
                WHEN prodis.jurusan_id = ? THEN 1
                WHEN jurusans.fakultas_id = ? THEN 2
                ELSE 3
            END ASC
        ', [$namaProdiUser, $jurusanIdUser, $fakultasIdUser])
            ->orderBy('prodis.nama_prodi', 'asc')
            ->limit(12)
            ->get([
                'prodis.id',
                'prodis.kode_pr',
                'prodis.nama_prodi',
                'jurusans.nama_jurusan',
                'jurusans.kode_jr',
                'fakultas.nama_fakultas',
                'fakultas.kode_fk',
            ]);

        $countMain = $mainResults->count();

        if ($countMain < 12) {

            $remaining = 12 - $countMain;

            $extraResults = Prodi::query()
                ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->where('jurusans.fakultas_id', '!=', $fakultasIdUser)
                ->whereNotIn('prodis.id', $mainResults->pluck('id'))
                ->orderBy('prodis.nama_prodi', 'asc')
                ->limit($remaining)
                ->get([
                    'prodis.id',
                    'prodis.kode_pr',
                    'prodis.nama_prodi',
                    'jurusans.nama_jurusan',
                    'jurusans.kode_jr',
                    'fakultas.nama_fakultas',
                    'fakultas.kode_fk',
                ]);

            $mainResults = $mainResults->concat($extraResults);
        }

        return $mainResults->map(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_pr ?? $item->kode_jr ?? $item->kode_fk ?? 'UNI',
                'prodi' => $item->nama_prodi,
                'jurusan' => $item->nama_jurusan,
                'fakultas' => $item->nama_fakultas,
            ];
        })->toArray();
    }

    public function getProdibyUser2()
    {
        $admin = Auth::user()?->admin;
        $userProdi = $admin ? $admin->prodi()->first() : null;

        if (! $userProdi) {
            return [];
        }

        $namaProdiUser = $userProdi->nama_prodi;
        $jurusanIdUser = $userProdi->jurusan_id;
        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id;

        $query = Prodi::query()
            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
            ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id');

        // --- 🔹 LOGIKA FILTER BERDASARKAN MK TYPE 🔹 ---
        // Jika mode Jurusan aktif, kunci prodi hanya di jurusan yang dipilih
        if ($this->mkType === 'mk-jurusan' && filled($this->jurusan_id) && $this->showMKModal) {
            $query->where('prodis.jurusan_id', $this->jurusan_id);
        }
        // Jika mode Fakultas aktif, kunci prodi hanya di fakultas yang dipilih
        elseif ($this->mkType === 'mk-fakultas' && filled($this->fakultas_id) && $this->showMKModal) {
            $query->where('jurusans.fakultas_id', $this->fakultas_id);
        }
        // Default: Filter berdasarkan Fakultas Admin (logika lama Anda)
        else {
            $query->where('jurusans.fakultas_id', $fakultasIdUser);
        }

        $mainResults = $query->orderByRaw('
            CASE 
                WHEN prodis.nama_prodi = ? THEN 0 
                WHEN prodis.jurusan_id = ? THEN 1 
                WHEN jurusans.fakultas_id = ? THEN 2 
                ELSE 3 
            END ASC
        ', [$namaProdiUser, $jurusanIdUser, $fakultasIdUser])
            ->orderBy('prodis.nama_prodi', 'asc')
            ->limit(12)
            ->get([
                'prodis.id',
                'prodis.kode_pr',
                'prodis.nama_prodi',
                'jurusans.nama_jurusan',
                'jurusans.kode_jr',
                'fakultas.nama_fakultas',
                'fakultas.kode_fk',
            ]);

        // --- 🔹 LOGIKA EXTRA RESULTS 🔹 ---
        // Hanya ambil extra results (lintas fakultas) jika tidak sedang dalam mode filter ketat
        $countMain = $mainResults->count();
        if ($countMain < 12 && empty($this->mkType)) {
            $remaining = 12 - $countMain;

            $extraResults = Prodi::query()
                ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->where('jurusans.fakultas_id', '!=', $fakultasIdUser)
                ->whereNotIn('prodis.id', $mainResults->pluck('id'))
                ->orderBy('prodis.nama_prodi', 'asc')
                ->limit($remaining)
                ->get([
                    'prodis.id',
                    'prodis.kode_pr',
                    'prodis.nama_prodi',
                    'jurusans.nama_jurusan',
                    'jurusans.kode_jr',
                    'fakultas.nama_fakultas',
                    'fakultas.kode_fk',
                ]);

            $mainResults = $mainResults->concat($extraResults);
        }

        return $mainResults->map(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_pr ?? $item->kode_jr ?? $item->kode_fk ?? 'UNI',
                'prodi' => $item->nama_prodi,
                'jurusan' => $item->nama_jurusan,
                'fakultas' => $item->nama_fakultas,
            ];
        })->toArray();
    }

    public function fetchProdi($query = '')
    {
        if (empty($query) || $this->prodi_id) {
            $this->prodi_results = $this->getProdibyUser();

            return;
        }
    }

    public function selectProdi($id, $prodiName)
    {
        $this->prodi_id = $id;
        $this->prodi_name_search = $prodiName;

        $data = Prodi::with(['jurusan_rel.fakultas_rel'])->find($id);
        if ($data) {
            $this->selected_kode_pr = $data->kode_pr ?? $data->jurusan_rel->kode_jr ?? $data->jurusan_rel->fakultas_rel->kode_fk ?? 'UNI';

            // $this->dispatch('prodi-selected', [
            //     'id' => $this->prodi_id,
            //     'kode' => $this->selected_kode_pr,
            //     'name' => $this->prodi_name_search
            // ]);
        }

        $this->prodi_results = $this->getProdibyUser();
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);
    }

    // public function selectProdi($prodiId, $prodiName)
    // {
    //     $this->prodi_id = $prodiId;
    //     $this->prodi_name_search = $prodiName;
    //     $this->getProdibyUser();
    //     $this->resetErrorBag(['prodi_id', 'prodi_name_search']);
    // }

    public function resetProdiInput()
    {
        $this->prodi_id = null;
        $this->selected_kode_pr = null;
        $this->prodi_name_search = '';

        // $this->dispatch('prodi-selected', [
        //     'id' => null,
        //     'kode' => null,
        //     'name' => ''
        // ]);

        $this->updatedProdiNameSearch('');
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);
    }
}
