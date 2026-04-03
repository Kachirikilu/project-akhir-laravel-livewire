<?php

namespace App\Livewire\Global;

use App\Models\Akademik\CPMK;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithCPMKSearchFilters
{
    use WithPagination;

    public $cpmkSearchQuery = '';
    public $cpmkSearchResults = [];
    public $cpmk_name = '';
    public $cpmk_id;
    public $cpmk_kode;
    public $cpmkNameSearch = '';
    public $cpmkResults = [];
    public $selectedCPMKId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $cpmk_id_array = [];
    public $cpmk_name_array = [];
    public $cpmk_kode_array = [];

    /**
     * Helper untuk mapping hasil agar seragam
     */
    // private function mapCPMK($collection)
    // {
    //     return $collection->map(fn ($c) => [
    //         'id' => $c->id,
    //         'kode' => $c->kode,
    //         'deskripsi' => $c->deskripsi,
    //     ])->toArray();
    // }

    private function mapCPMK($collection)
{
    // Pastikan relasi sub_cpmks di-load (asumsi nama relasi di model CPMK adalah sub_cpmks)
    return $collection->map(fn ($c) => [
        'id' => $c->id,
        'kode' => $c->kode,
        'deskripsi' => $c->deskripsi,
        // Ambil data Sub-CPMK
        'sub_cpmk' => $c->sub_cpmks->map(fn ($sub) => [
            'id' => $sub->id,
            'kode' => $sub->kode,
            'deskripsi' => $sub->deskripsi,
            'materi' => $sub->materi,
            'metodologi' => $sub->metodologi,
            'indikator' => $sub->indikator,
            'metode' => $sub->metode,
            'bobot' => $sub->bobot ?? 0,
            'tugas' => $sub->tugas,
            'w_tugas' => $sub->w_tugas,
            'w_mandiri' => $sub->w_mandiri,

        ])->toArray(),
        'total_bobot' => $c->sub_cpmks->sum('bobot') 
    ])->toArray();
}

    public function inputCPMKFilter()
    {
        $search = trim($this->cpmkSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->cpmk_name) {
            $this->cpmkSearchResults = $this->mapCPMK(
                CPMK::query()->with(['sub_cpmks'])
                ->searchCPMK($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->cpmk_name) {
            $this->cpmkSearchResults = $this->getCPMKbyUser();
        } else {
            $this->cpmkSearchResults = [];
        }
    }

    public function resetCPMKFilter()
    {
        $this->reset(['selectedCPMKId', 'cpmk_name', 'cpmkSearchQuery', 'cpmk_kode']);
        $this->resetPage();
    }

    public function selectCPMKForFilter($id)
    {
        $data = CPMK::
        with(['sub_cpmks'])->
        find($id);

        if ($data) {
            $this->selectedCPMKId = $id;
            $this->cpmk_kode = $data->kode;
            $this->cpmk_name = $data->deskripsi;
            $this->cpmkSearchQuery = $data->deskripsi;
            $this->cpmkSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedCPMKNameSearch($value)
    {
        $this->cpmk_id = null;
        $this->cpmk_kode = null;
        $this->resetErrorBag(['cpmk_id', 'cpmkNameSearch']);

        $query = CPMK::query()->with(['sub_cpmks']);

        if (trim(strlen($value)) > 0) {
            $results = $query->searchCPMK($value)->limit(12)->get();
            $this->cpmkResults = $this->mapCPMK($results);

            // Exact Match Logic
            $exactMatch = $results->first(function ($s) use ($value) {
                return strtolower($s->deskripsi) === strtolower($value) 
                    || strtolower($s->kode) === strtolower($value);
            });

            if ($exactMatch) {
                $this->cpmk_id = $exactMatch->id;
                $this->cpmk_kode = $exactMatch->kode;
                $this->cpmkNameSearch = $exactMatch->deskripsi;
                $this->cpmkResults = [];
            }
        } else {
            if (Auth::user()->prodi_id) {
                $this->cpmkResults = $this->getCPMKbyUser();
            } else {
                $this->cpmkResults = $this->mapCPMK(
                    $query->orderBy('cpmks.deskripsi')->limit(12)->get()
                );
            }
        }
    }

    public function getCPMKbyUser()
    {
        $user = Auth::user();
        $prodiId = $user->prodi_id ?? null;

        $query = CPMK::query()->with(['sub_cpmks']);
        
        if (!$prodiId) {
            $defaultCPMK = $query
                ->latest()
                ->limit(12)
                ->get();
            return $this->mapCPMK($defaultCPMK);
        }

        $mainResults = $query
            ->whereHas('rps.matkul_rel.prodis', function($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = CPMK::whereNotIn('id', $mainResults->pluck('id'))->with(['sub_cpmks'])
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapCPMK($mainResults);
    }

    public function fetchCPMK($query = '')
    {
        if (empty($query) || $this->cpmk_id) {
            $this->cpmkResults = $this->getCPMKbyUser();
        }

        return;
    }


    public function selectCPMK($id, $cpmkName)
    {
        $this->cpmk_id = $id;
        $this->cpmkNameSearch = $cpmkName;
        $this->cpmkResults = $this->getCPMKbyUser();

        $data = CPMK::with(['sub_cpmks'])->find($id);
        if ($data) {
            $this->cpmk_kode = $data->kode;
        }

        if (method_exists($this, 'fetchCPMK')) {
            $this->fetchCPMK('');
        }

        $this->resetErrorBag(['cpmk_id', 'cpmkNameSearch']);
    }
    public function selectCPMKArray($id)
    {
        $data = CPMK::find($id);
        if ($data && ! in_array($id, $this->cpmk_id_array)) {
            $this->cpmk_id_array[] = $id;
            $this->cpmk_name_array[] = $data->deskripsi;
            $this->cpmk_kode_array[] = $data->kode;
        }
    }

    public function resetCPMKInput()
    {
        $this->reset(['cpmk_id', 'cpmk_kode', 'cpmkNameSearch']);
        $this->cpmkResults = $this->getCPMKbyUser();
    }

    public function resetCPMKArray()
    {
        $this->cpmk_id_array = [];
        $this->cpmk_name_array = [];
        $this->cpmk_kode_array = [];
        $this->cpmkNameSearch = '';
    }
}