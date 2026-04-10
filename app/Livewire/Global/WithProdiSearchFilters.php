<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithProdiSearchFilters
{
    use WithPagination;

    public $prSearchQuery = '';

    public $prSearchResults = [];

    public $modePr = '';

    public $pr_id;

    public $pr_id_array = [];

    public $pr_name;

    public $pr_items;

    public $pr_items_array = [];

    public $prNameSearch = '';

    public $prResults = [];

    public $selectedPrId = null;

    public $mkType = '';

    public $showMKModal = false;

    private function mapProdi($collection)
    {
        return $collection->map(fn ($p) => [
            'id' => $p->id,
            'kode' => $p->kode,
            'prodi' => $p->prodi,
            'jurusan' => $p->jurusanJr,
            'fakultas' => $p->fakultasFk,
            'strata' => $p->strata,
        ])->toArray();
    }

    private function prQuery()
    {
        return Prodi::query()->with(['jr_rel', 'jr_rel.fk_rel']);
    }

    private function itemsPr($p)
    {
        if (! $p) {
            return null;
        }
        return [
            'id' => $p->id,
            'kode' => $p->kode,
            'slot1' => $p->prodi,
            'slot2' => $p->jurusanJr,
            'slot3' => $p->fakultasFk,
        ];
    }

    public function inputProdiFilter()
    {
        $search = trim($this->prSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->pr_name) {
            $this->prSearchResults = $this->mapProdi(
                $this->prQuery()
                    ->searchProdi($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->pr_name) {
            $this->prSearchResults = $this->getProdibyUser();
        } else {
            $this->prSearchResults = [];
        }
    }

    public function resetProdiFilter()
    {
        $this->reset(['selectedPrId', 'prSearchQuery', 'pr_name', 'pr_items']);
        $this->resetPage();
    }

    public function selectProdiForFilter($id)
    {
        $data = $this->prQuery()->find($id);

        if ($data) {
            $this->selectedPrId = $id;
            $this->pr_name = $data->prodi;
            $this->prSearchQuery = $data->prodi;
            $this->pr_items = $this->itemsPr($data);
            $this->prSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedProdiNameSearch($value)
    {
        $this->pr_id = null;
        $this->pr_items = null;
        $this->resetErrorBag(['pr_id', 'prNameSearch']);

        $input = str($value)->lower()->trim();

        // Jika input kosong, kembalikan ke daftar default yang sudah difilter
        if (empty($input->toString())) {
            $this->prResults = $this->getProdibyUser();

            return;
        }

        $query = $this->prQuery()->select('prodis.*');

        // --- TAMBAHKAN LOGIKA FILTER DI SINI ---
        if (($this->mkType == 2) && filled($this->jr_id) && $this->showMKModal) {
            $query->where('jr_id', $this->jr_id);
        } elseif (($this->mkType == 3) && filled($this->fk_id) && $this->showMKModal) {
            $query->whereHas('jr_rel', fn ($q) => $q->where('fk_id', $this->fk_id));
        }
        // 1. Logika shortcut 'uni' untuk mkType 4
        if ($this->modePr !== 'single' && $input->toString() === 'uni' && $this->mkType == 4) {
            $allProdis = $query->get();
            foreach ($allProdis as $p) {
                if (! in_array($p->id, $this->pr_id_array)) {
                    $this->pr_id_array[] = $p->id;
                    $this->pr_items_array[] = $this->itemsPr($p);
                }
            }
            $this->prNameSearch = '';
            $this->prResults = $this->getProdibyUser();

            return;
        }

        // 2. Jalankan Query Pencarian Biasa (untuk filter dropdown)
        $results = $query->searchProdi($value)->limit(12)->get();
        $this->prResults = $this->mapProdi($results);

        // 3. Pencocokan "Exact Match" yang Diperluas (Leveling)
        $matches = $results->filter(function ($prodi) use ($input) {
            $namaProdi = str($prodi->prodi)->lower()->trim();
            $kodeProdi = str($prodi->kode)->lower()->trim();

            $kodeJurusan = $kodeProdi;
            $kodeFakultas = $kodeProdi;

            if ($this->mkType >= 2) {
                $kodeJurusan = str($prodi->jr_rel?->kode ?? '')->lower()->trim();
            }
            if ($this->mkType >= 3) {
                $kodeFakultas = str($prodi->jr_rel?->fk_rel?->kode ?? '')->lower()->trim();
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
            if ($this->modePr == 'single') {
                $exactMatch = $matches->first();
                $this->pr_id = $exactMatch->id;
                $this->pr_items = $this->itemsPr($exactMatch);
                $this->prNameSearch = $exactMatch->prodi;
            } else {
                foreach ($matches as $match) {
                    if (! in_array($match->id, $this->pr_id_array)) {
                        $this->pr_id_array[] = $match->id;
                        $this->pr_items_array[] = $this->itemsPr($match);
                    }
                }
                $this->prNameSearch = '';
            }
            $this->prResults = $this->getProdibyUser();
        }
    }

    public function getProdibyUser()
    {
        $user = Auth::user();
        $prodiId = $user?->pr_id;
        $jurusanId = $user->jr_id ?? null;
        $fakultasId = $user->fk_id ?? null;

        $query = $this->prQuery();

        if (! $prodiId) {
            $defaultProdis = $query
                ->orderBy('nama_prodi', 'asc')
                ->limit(12)
                ->get();

            return $this->mapProdi($defaultProdis);
        }

        if (($this->mkType == 2) && filled($this->jr_id) && $this->showMKModal) {
            $query->where('jr_id', $this->jr_id);
        } elseif (($this->mkType == 3) && filled($this->fk_id) && $this->showMKModal) {
            $query->whereHas('jr_rel', fn ($q) => $q->where('fk_id', $this->fk_id));
        } else {
            $query->whereHas('jr_rel', fn ($q) => $q->where('fk_id', $fakultasId));
        }

        $mainResults = $query->get()->sortBy(function ($p) use ($prodiId, $jurusanId, $fakultasId) {
            if ($p->id === $prodiId) {
                return 0;
            }
            if ($p->jr_id === $jurusanId) {
                return 1;
            }
            if ($p->fk_id === $fakultasId) {
                return 2;
            }

            return 3;
        })->take(12);

        if ($mainResults->count() < 12) {
            $extra = $this->prQuery()
                ->whereHas('jr_rel', fn ($q) => $q->where('fk_id', '!=', $fakultasId))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapProdi($mainResults);
    }

    // public function fetchProdi2($query = '', $mode = 'single')
    // {
    //     $this->modePr = $mode;

    //     if (empty(trim($query))) {
    //         $this->prResults = $this->getProdibyUser();

    //         return;
    //     }

    //     $results = $this->prQuery()
    //         ->searchProdi($query)
    //         ->limit(12)
    //         ->get();

    //     $this->prResults = $this->mapProdi($results);
    // }

    public function fetchProdi($query = '', $mode = 'single')
    {

        $this->modePr = $mode;
        if (empty($query) || $this->pr_id) {
            $this->prResults = $this->getProdibyUser();

            return;
        }
    }

    public function selectProdi($id, $prodiName)
    {
        $this->pr_id = $id;
        $this->prNameSearch = $prodiName;

        $data = $this->prQuery()->find($id);
        if ($data) {
            $this->pr_items = $this->itemsPr($data);
        }

        $this->prResults = $this->getProdibyUser();
        $this->resetErrorBag(['pr_id', 'prNameSearch']);
    }

    public function selectProdiArray($id)
    {
        $data = $this->prQuery()->find($id);

        if ($data && ! in_array($id, (array) $this->pr_id_array)) {
            $this->pr_id_array[] = $id;
            $this->pr_items_array[] = $this->itemsPr($data);
            $this->prodi_search = '';
        }
    }

    public function resetProdiInput()
    {
        $this->pr_id = null;
        $this->pr_items = null;
        $this->prNameSearch = '';

        $this->updatedProdiNameSearch('');
        $this->resetErrorBag(['pr_id', 'prNameSearch']);
    }

    public function resetProdiArray()
    {
        $this->pr_id_array = [];
        $this->pr_items_array = [];
        $this->prNameSearch = '';
    }
}
