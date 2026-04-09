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

    public $modeProdi = '';

    public $prodi_id;

    public $prodi_id_array = [];

    public $prodi_name;

    public $prodi_items;

    public $prodi_items_array = [];

    public $prodiNameSearch = '';

    public $prodiResults = [];

    public $selectedProdiId = null;

    public $mkType = '';

    public $showMKModal = false;

    private function mapProdi($collection)
    {
        return $collection->map(fn ($pr) => [
            'id' => $pr->id,
            'kode' => $pr->kode,
            'prodi' => $pr->prodi,
            'jurusan' => $pr->jurusanJr,
            'fakultas' => $pr->fakultasFk,
            'strata' => $pr->strata,
        ])->toArray();
    }

    private function prQuery()
    {
        return Prodi::query()->with(['jurusan_rel', 'jurusan_rel.fakultas_rel']);
    }

    private function itemsPr($pr)
    {
        if (! $pr) {
            return null;
        }
        return [
            'id' => $pr->id,
            'kode' => $pr->kode,
            'name' => $pr->prodi,
            'name2' => $pr->jurusanJr,
            'name3' => $pr->fakultasFk,
        ];
    }

    public function inputProdiFilter()
    {
        $search = trim($this->prodiSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->prodi_name) {
            $this->prodiSearchResults = $this->mapProdi(
                $this->prQuery()
                    ->searchProdi($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->prodi_name) {
            $this->prodiSearchResults = $this->getProdibyUser();
        } else {
            $this->prodiSearchResults = [];
        }
    }

    public function resetProdiFilter()
    {
        $this->reset(['selectedProdiId', 'prodiSearchQuery', 'prodi_name', 'prodi_items']);
        $this->resetPage();
    }

    public function selectProdiForFilter($id)
    {
        $data = $this->prQuery()->find($id);

        if ($data) {
            $this->selectedProdiId = $id;
            $this->prodi_name = $data->prodi;
            $this->prodiSearchQuery = $data->prodi;
            $this->prodi_items = $this->itemsPr($data);
            $this->prodiSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedProdiNameSearch($value)
    {
        $this->prodi_id = null;
        $this->prodi_items = null;
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);

        $input = str($value)->lower()->trim();

        // Jika input kosong, kembalikan ke daftar default yang sudah difilter
        if (empty($input->toString())) {
            $this->prodiResults = $this->getProdibyUser();

            return;
        }

        $query = $this->prQuery()->select('prodis.*');

        // --- TAMBAHKAN LOGIKA FILTER DI SINI ---
        if (($this->mkType == 2) && filled($this->jurusan_id) && $this->showMKModal) {
            $query->where('jurusan_id', $this->jurusan_id);
        } elseif (($this->mkType == 3) && filled($this->fakultas_id) && $this->showMKModal) {
            $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->fakultas_id));
        }
        // 1. Logika shortcut 'uni' untuk mkType 4
        if ($this->modeProdi !== 'single' && $input->toString() === 'uni' && $this->mkType == 4) {
            $allProdis = $query->get();
            foreach ($allProdis as $p) {
                if (! in_array($p->id, $this->prodi_id_array)) {
                    $this->prodi_id_array[] = $p->id;
                    $this->prodi_items_array[] = $this->itemsPr($p);
                }
            }
            $this->prodiNameSearch = '';
            $this->prodiResults = $this->getProdibyUser();

            return;
        }

        // 2. Jalankan Query Pencarian Biasa (untuk filter dropdown)
        $results = $query->searchProdi($value)->limit(12)->get();
        $this->prodiResults = $this->mapProdi($results);

        // 3. Pencocokan "Exact Match" yang Diperluas (Leveling)
        $matches = $results->filter(function ($prodi) use ($input) {
            $namaProdi = str($prodi->prodi)->lower()->trim();
            $kodeProdi = str($prodi->kode)->lower()->trim();

            $kodeJurusan = $kodeProdi;
            $kodeFakultas = $kodeProdi;

            if ($this->mkType >= 2) {
                $kodeJurusan = str($prodi->jurusan_rel?->kode ?? '')->lower()->trim();
            }
            if ($this->mkType >= 3) {
                $kodeFakultas = str($prodi->jurusan_rel?->fakultas_rel?->kode ?? '')->lower()->trim();
            }

            $namaStrata = str($prodi->strata)->lower()->trim();
            $inisialStrata = match ($namaStrata->toString()) {
                'sarjana' => 's1', 'magister' => 's2', 'doktor' => 's3', default => ''
            };

            $possibilities = [
                $namaProdi->toString(),
                $kodeProdi->toString(),
                $kodeJurusan->toString(),
                $kodeFakultas->toString(),
                "$inisialStrata $namaProdi",
                "$namaStrata $namaProdi",
                "$inisialStrata$namaProdi",
            ];

            return in_array($input->toString(), $possibilities);
        });

        // 4. Eksekusi Hasil Match
        if ($matches->isNotEmpty()) {
            if ($this->modeProdi == 'single') {
                $exactMatch = $matches->first();
                $this->prodi_id = $exactMatch->id;
                $this->prodi_items = $this->itemsPr($exactMatch);
                $this->prodiNameSearch = $exactMatch->prodi;
            } else {
                foreach ($matches as $match) {
                    if (! in_array($match->id, $this->prodi_id_array)) {
                        $this->prodi_id_array[] = $match->id;
                        $this->prodi_items_array[] = $this->itemsPr($match);
                    }
                }
                $this->prodiNameSearch = '';
            }
            $this->prodiResults = $this->getProdibyUser();
        }
    }

    public function getProdibyUser()
    {
        $user = Auth::user();
        $prodiId = $user?->prodi_id;
        $jurusanId = $user->jurusan_id ?? null;
        $fakultasId = $user->fakultas_id ?? null;

        $query = $this->prQuery();

        if (! $prodiId) {
            $defaultProdis = $query
                ->orderBy('nama_prodi', 'asc')
                ->limit(12)
                ->get();

            return $this->mapProdi($defaultProdis);
        }

        if (($this->mkType == 2) && filled($this->jurusan_id) && $this->showMKModal) {
            $query->where('jurusan_id', $this->jurusan_id);
        } elseif (($this->mkType == 3) && filled($this->fakultas_id) && $this->showMKModal) {
            $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->fakultas_id));
        } else {
            $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $fakultasId));
        }

        $mainResults = $query->get()->sortBy(function ($p) use ($prodiId, $jurusanId, $fakultasId) {
            if ($p->id === $prodiId) {
                return 0;
            }
            if ($p->jurusan_id === $jurusanId) {
                return 1;
            }
            if ($p->fakultas_id === $fakultasId) {
                return 2;
            }

            return 3;
        })->take(12);

        if ($mainResults->count() < 12) {
            $extra = $this->prQuery()
                ->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', '!=', $fakultasId))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapProdi($mainResults);
    }

    // public function fetchProdi2($query = '', $mode = 'single')
    // {
    //     $this->modeProdi = $mode;

    //     if (empty(trim($query))) {
    //         $this->prodiResults = $this->getProdibyUser();

    //         return;
    //     }

    //     $results = $this->prQuery()
    //         ->searchProdi($query)
    //         ->limit(12)
    //         ->get();

    //     $this->prodiResults = $this->mapProdi($results);
    // }

    public function fetchProdi($query = '', $mode = 'single')
    {

        $this->modeProdi = $mode;
        if (empty($query) || $this->prodi_id) {
            $this->prodiResults = $this->getProdibyUser();

            return;
        }
    }

    public function selectProdi($id, $prodiName)
    {
        $this->prodi_id = $id;
        $this->prodiNameSearch = $prodiName;

        $data = $this->prQuery()->find($id);
        if ($data) {
            $this->prodi_items = $this->itemsPr($data);
        }

        $this->prodiResults = $this->getProdibyUser();
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }

    public function selectProdiArray($id)
    {
        $data = $this->prQuery()->find($id);

        if ($data && ! in_array($id, (array) $this->prodi_id_array)) {
            $this->prodi_id_array[] = $id;
            $this->prodi_items_array[] = $this->itemsPr($data);
            $this->prodi_search = '';
        }
    }

    public function resetProdiInput()
    {
        $this->prodi_id = null;
        $this->prodi_items = null;
        $this->prodiNameSearch = '';

        $this->updatedProdiNameSearch('');
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }

    public function resetProdiArray()
    {
        $this->prodi_id_array = [];
        $this->prodi_items_array = [];
        $this->prodiNameSearch = '';
    }
}
