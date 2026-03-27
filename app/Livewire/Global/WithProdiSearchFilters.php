<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Prodi;
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

    public $mkType = '';

    public $showMKModal = false;

    public function inputProdiFilter()
    {
        $search = trim($this->prodiSearchQuery);
        $searchTerm = '%'.$search.'%';

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->prodi_name) {
            $this->prodiSearchResults = Prodi::with(['jurusan_rel.fakultas_rel'])
                ->searchProdi($searchTerm)
                ->limit(12)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'kode' => $p->kode,
                    'prodi' => $p->prodi,
                    'jurusan' => $p->jurusan,
                    'fakultas' => $p->fakultas,
                ])->toArray();

        } elseif (empty($search) || $this->prodi_name) {
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
            $this->prodi_kode = $data->kode;
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

            $query->searchProdi($searchTerm);
            $results = $query->limit(12)->get();

            // Mapping Hasil Pencarian
            $this->prodiResults = $results->map(function ($prodi) {
                return [
                    'id' => $prodi->id,
                    'kode' => $prodi->kode,
                    'prodi' => $prodi->prodi,
                    'jurusan' => $prodi->jurusan,
                    'fakultas' => $prodi->fakultas,
                    'strata' => $prodi->strata,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($prodi) use ($value) {
                $input = str($value)->lower()->trim();

                $namaProdi = str($prodi->prodi)->lower()->trim();
                $kodeProdi = str($prodi->kode)->lower()->trim();
                $namaStrata = str($prodi->strata)->lower()->trim();

                $inisialStrata = match ($namaStrata->toString()) {
                    'sarjana' => 's1',
                    'magister' => 's2',
                    'doktor' => 's3',
                    default => ''
                };

                $possibilities = [
                    $namaProdi->toString(),
                    $kodeProdi->toString(),
                    "$inisialStrata $namaProdi",
                    "$namaStrata $namaProdi",
                    "$inisialStrata$namaProdi",
                ];

                return in_array($input->toString(), $possibilities);
            });

            if ($exactMatch) {
                $this->prodi_id = $exactMatch->id;
                $this->prodi_kode = $exactMatch->kode;
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
                        'kode' => $p->kode,
                        'prodi' => $p->prodi,
                        'jurusan' => $p->jurusan,
                        'fakultas' => $p->fakultas,
                    ])->toArray();
            } else {
                $this->prodiResults = $this->getProdibyUser();
            }
        }
    }

    // public function getProdibyUser()
    // {
    //     $user = Auth::user()?->admin ?? Auth::user()?->dosen ?? Auth::user()?->mahasiswa;
    //     $userProdi = $user ? $user->prodi()->first() : null;

    //     $prodiIdUser = $userProdi?->id ?? null;

    //     if (! $prodiIdUser) {
    //         return Prodi::query()
    //             ->orderBy('nama_prodi', 'asc')
    //             ->limit(12)
    //             ->get()
    //             ->map(fn ($f) => [
    //                 'id' => $f->id,
    //                 'kode' => $f->kode,
    //                 'prodi' => $f->prodi,
    //                 'jurusan' => $f->jurusan,
    //                 'fakultas' => $f->fakultas,
    //             ])->toArray();
    //     }

    //     $namaProdiUser = $userProdi->prodi;
    //     $jurusanIdUser = $userProdi->jurusan_id;
    //     $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id;

    //     $query = Prodi::query()
    //         ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
    //         ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id');

    //     if (($this->mkType === 'mk-jurusan' || $this->mkType === 2) && filled($this->jurusan_id) && $this->showMKModal) {
    //         $query->where('prodis.jurusan_id', $this->jurusan_id);
    //     } elseif (($this->mkType === 'mk-fakultas' || $this->mkType === 3) && filled($this->fakultas_id) && $this->showMKModal) {
    //         $query->where('jurusans.fakultas_id', $this->fakultas_id);
    //     } else {
    //         $query->where('jurusans.fakultas_id', $fakultasIdUser);
    //     }

    //     $mainResults = $query->orderByRaw('
    //         CASE
    //             WHEN prodis.nama_prodi = ? THEN 0
    //             WHEN prodis.jurusan_id = ? THEN 1
    //             WHEN jurusans.fakultas_id = ? THEN 2
    //             ELSE 3
    //         END ASC
    //     ', [$namaProdiUser, $jurusanIdUser, $fakultasIdUser])
    //         ->orderBy('prodis.nama_prodi', 'asc')
    //         ->limit(12)
    //         ->get([
    //             'prodis.id',
    //             'prodis.kode_pr',
    //             'prodis.nama_prodi',
    //             'jurusans.nama_jurusan',
    //             'jurusans.kode_jr',
    //             'fakultas.nama_fakultas',
    //             'fakultas.kode_fk',
    //         ]);

    //     $countMain = $mainResults->count();
    //     if ($countMain < 12 && empty($this->mkType)) {
    //         $remaining = 12 - $countMain;

    //         $extraResults = Prodi::query()
    //             ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
    //             ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
    //             ->where('jurusans.fakultas_id', '!=', $fakultasIdUser)
    //             ->whereNotIn('prodis.id', $mainResults->pluck('id'))
    //             ->orderBy('prodis.nama_prodi', 'asc')
    //             ->limit($remaining)
    //             ->get([
    //                 'prodis.id',
    //                 'prodis.kode_pr',
    //                 'prodis.nama_prodi',
    //                 'prodis.nama_strata',
    //                 'jurusans.nama_jurusan',
    //                 'jurusans.kode_jr',
    //                 'fakultas.nama_fakultas',
    //                 'fakultas.kode_fk'
    //             ]);

    //         $mainResults = $mainResults->concat($extraResults);
    //     }

    //     return $mainResults->map(function ($item) {
    //         return [
    //             'id' => $item->id,
    //             'kode' => $item->kode_pr ?? $item->kode_jr ?? $item->kode_fk ?? 'UNI',
    //             'prodi' => $item->prodi,
    //             'jurusan' => $item->nama_jurusan,
    //             'fakultas' => $item->nama_fakultas,
    //         ];
    //     })->toArray();
    // }

    public function getProdibyUser()
    {
        $user = Auth::user()?->admin ?? Auth::user()?->dosen ?? Auth::user()?->mahasiswa;
        $userProdi = $user ? $user->prodi()->first() : null;

        // Jika tidak ada user/prodi, kembalikan 12 prodi pertama secara simpel
        if (! $userProdi) {
            return Prodi::with(['jurusan_rel.fakultas_rel'])
                ->orderBy('nama_prodi', 'asc')
                ->limit(12)
                ->get()
                ->map(fn ($f) => [
                    'id' => $f->id,
                    'kode' => $f->kode,
                    'prodi' => $f->prodi,
                    'jurusan' => $f->jurusan,
                    'fakultas' => $f->fakultas,
                ])->toArray();
        }

        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id;

        $query = Prodi::with(['jurusan_rel.fakultas_rel']);

        if (in_array($this->mkType, ['mk-jurusan', 2]) && filled($this->jurusan_id) && $this->showMKModal) {
            $query->where('jurusan_id', $this->jurusan_id);
        } elseif (in_array($this->mkType, ['mk-fakultas', 3]) && filled($this->fakultas_id) && $this->showMKModal) {
            $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->fakultas_id));
        } else {
            $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $fakultasIdUser));
        }

        $mainResults = $query->get()->sortBy(function ($p) use ($userProdi, $fakultasIdUser) {
            if ($p->id === $userProdi->id) {
                return 0;
            }
            if ($p->jurusan_id === $userProdi->jurusan_id) {
                return 1;
            }
            if ($p->jurusan_rel?->fakultas_id === $fakultasIdUser) {
                return 2;
            }

            return 3;
        })->take(12);

        if ($mainResults->count() < 12 && empty($this->mkType)) {
            $extra = Prodi::with(['jurusan_rel.fakultas_rel'])
                ->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', '!=', $fakultasIdUser))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mainResults->map(fn ($f) => [
            'id' => $f->id,
            'kode' => $f->kode,
            'prodi' => $f->prodi,
            'jurusan' => $f->jurusan,
            'fakultas' => $f->fakultas,
        ])->toArray();
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
            $this->prodi_kode = $data->kode;
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
            $this->prodi_kode_array[] = $data->kode;
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
