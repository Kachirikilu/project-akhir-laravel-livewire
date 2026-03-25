<?php

namespace App\Livewire\Global;

use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithProdiSearchFilters
{
    use WithPagination;

    public $prodiSearchQuery = '';

    public $prodiSearchResults = [];

    public $prodi_name = '';

    public $prodi_name_array = [];

    public $prodi_id;

    public $prodi_id_array = [];

    public $prodi_kode;

    public $prodi_kode_array = [];

    public $prodiNameSearch = '';

    public $prodiResults = [];

    public $selectedProdiId = null;

    // Punya WithMatkulModal
    public $mkType = '';

    public $showMKModal = false;

    public function inputProdiFilter()
    {
        $searchTerm = '%'.$this->prodiSearchQuery.'%';

        if ((strlen($this->prodiSearchQuery) > 1 || is_numeric($this->prodiSearchQuery)) && ! $this->prodi_name) {
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
                    'kode' => $p->kode ?? 'UNI',
                    'prodi' => $p->prodi,
                    'jurusan' => $p->jurusan_rel?->jurusan,
                    'fakultas' => $p->jurusan_rel?->fakultas_rel?->fakultas,
                ])->toArray();
        } elseif (empty($this->prodiSearchQuery) || $this->prodi_name) {
            $this->prodiSearchResults = $this->getProdibyUser();
        } else {
            $this->prodiSearchResults = [];
        }
    }

    public function resetProdiFilter()
    {
        $this->reset(['selectedProdiId', 'prodi_name', 'prodiSearchQuery', 'prodi_kode']);
        $this->resetPage();
    }

    public function selectProdiForFilter($id)
    {
        $data = Prodi::with(['jurusan_rel.fakultas_rel'])->find($id);

        if ($data) {
            $this->selectedProdiId = $id;
            $this->prodi_kode = $data->kode ?? 'UNI';
            $this->prodi_name = $data->prodi;
            $this->prodiSearchQuery = $data->prodi;
            $this->prodiSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedProdiNameSearch($value)
    {
        // 1. Reset State Awal
        $this->prodi_id = null;
        $this->prodi_kode = null;
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);

        // 2. Inisialisasi Query Dasar (Gunakan select prodis.* untuk menghindari ID tertimpa join)
        $query = Prodi::query()
            ->select('prodis.*')
            ->with(['jurusan_rel.fakultas_rel']);

        // 3. PRIORITAS: Filter Berdasarkan Mode Mata Kuliah (Scope Constraints)
        if ($this->showMKModal) {
            if (($this->mkType === 'mk-jurusan' || $this->mkType === 2) && filled($this->jurusan_id)) {
                $query->where('prodis.jurusan_id', $this->jurusan_id);
            } elseif (($this->mkType === 'mk-fakultas' || $this->mkType === 3) && filled($this->fakultas_id)) {
                $query->whereHas('jurusan_rel', function ($q) {
                    $q->where('fakultas_id', $this->fakultas_id);
                });
            }
        }

        // 4. Logika Pencarian (Jika User Mengetik Sesuatu)
        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('prodis.nama_prodi', 'like', $searchTerm)
                    ->orWhere('prodis.kode_pr', 'like', $searchTerm)
                    ->orWhere('prodis.id', 'like', $searchTerm)
                    ->orWhereHas('jurusan_rel', function ($sq) use ($searchTerm) {
                        $sq->where('nama_jurusan', 'like', $searchTerm)
                            ->orWhere('kode_jr', 'like', $searchTerm)
                            ->orWhereHas('fakultas_rel', function ($ssq) use ($searchTerm) {
                                $ssq->where('nama_fakultas', 'like', $searchTerm)
                                    ->orWhere('kode_fk', 'like', $searchTerm);
                            });
                    });
            });

            $results = $query->limit(12)->get();

            // Mapping Hasil Pencarian
            $this->prodiResults = $results->map(function ($prodi) {
                return [
                    'id' => $prodi->id,
                    'kode' => $prodi->kode_pr ?? 'UNI',
                    'prodi' => $prodi->prodi,
                    'jurusan' => $prodi->jurusan_rel?->jurusan,
                    'fakultas' => $prodi->jurusan_rel?->fakultas_rel?->fakultas,
                ];
            })->toArray();

            // Exact Match Logic
            $exactMatch = $results->first(function ($prodi) use ($value) {
                $input = str($value)->lower()->trim();

                return $input->is([str($prodi->nama_prodi)->lower(), str($prodi->kode_pr)->lower()]);
            });

            if ($exactMatch) {
                $this->prodi_id = $exactMatch->id;
                $this->prodi_kode = $exactMatch->kode ?? 'UNI';
                $this->prodiNameSearch = $exactMatch->prodi;
                $this->prodiResults = [];
            }
        }
        // 5. Default State (Jika input kosong)
        else {
            if ($this->showMKModal && ($this->jurusan_id || $this->fakultas_id)) {
                $this->prodiResults = $query->orderBy('prodis.nama_prodi')
                    ->limit(12)
                    ->get()
                    ->map(fn ($p) => [
                        'id' => $p->id,
                        'kode' => $p->kode ?? 'UNI',
                        'prodi' => $p->prodi,
                        'jurusan' => $p->jurusan_rel?->jurusan,
                        'fakultas' => $p->jurusan_rel?->fakultas_rel?->fakultas,
                    ])->toArray();
            } else {
                $this->prodiResults = $this->getProdibyUser();
            }
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

        $query = Prodi::query()
            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
            ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id');

        // --- 🔹 LOGIKA FILTER BERDASARKAN MK TYPE 🔹 ---
        if (($this->mkType === 'mk-jurusan' || $this->mkType === 2) && filled($this->jurusan_id) && $this->showMKModal) {
            $query->where('prodis.jurusan_id', $this->jurusan_id);
        }
        elseif (($this->mkType === 'mk-fakultas' || $this->mkType === 3) && filled($this->fakultas_id) && $this->showMKModal) {
            $query->where('jurusans.fakultas_id', $this->fakultas_id);
        }
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
                'kode' => $item->kode ?? 'UNI',
                'prodi' => $item->prodi,
                'jurusan' => $item->jurusan,
                'fakultas' => $item->fakultas,
            ];
        })->toArray();
    }

    public function fetchProdi($query = '')
    {
        if (empty($query) || $this->prodi_id) {
            $this->prodiResults = $this->getProdibyUser();

            return;
        }
    }

    public function selectProdi($id, $prodiName)
    {
        $this->prodi_id = $id;
        $this->prodiNameSearch = $prodiName;

        $data = Prodi::with(['jurusan_rel.fakultas_rel'])->find($id);
        if ($data) {
            $this->prodi_kode = $data->kode ?? 'UNI';
        }

        $this->prodiResults = $this->getProdibyUser();
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }

    public function selectProdiArray($id)
    {
        $data = Prodi::find($id);
        if ($data && ! in_array($id, $this->prodi_id_array)) {
            $this->prodi_id_array[] = $id;
            $this->prodi_name_array[] = $data->prodi;
            $this->prodi_kode_array[] = $data->kode ?? 'UNI';
        }
    }

    public function resetProdiInput()
    {
        $this->prodi_id = null;
        $this->prodi_kode = null;
        $this->prodiNameSearch = '';

        $this->updatedProdiNameSearch('');
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }

    public function resetProdiArray()
    {
        $this->prodi_id_array = [];
        $this->prodi_name_array = [];
        $this->prodi_kode_array = [];
        $this->prodiNameSearch = '';
    }
}
