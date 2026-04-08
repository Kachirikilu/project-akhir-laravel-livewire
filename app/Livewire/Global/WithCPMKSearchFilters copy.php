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

    // public $sub_items_array = [];

    private function mapCPMK($collection)
    {
        return $collection->map(function ($c) {
            $cpmkRefIds = $c->referensis->pluck('id')->toArray();

            return [
                'id' => $c->id,
                'kode' => $c->kode,
                'deskripsi' => $c->deskripsi,

                'scpmk' => $c->scpmks->map(function ($sub) use ($cpmkRefIds) {
                    return [
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
                        'ref' => $sub->referensis
                            ->filter(fn ($sub_ref) => ! in_array($sub_ref->id, $cpmkRefIds))
                            ->map(fn ($sub_ref) => [
                                'id' => $sub_ref->id,
                                'kode' => $sub_ref->kode,
                                'judul' => $sub_ref->judul,
                                'penulis' => $sub_ref->penulis,
                                'penerbit' => $sub_ref->penerbit,
                                'tahun' => $sub_ref->tahun,
                                'link' => $sub_ref->link,

                            ]),
                    ];
                })->toArray(),

                // Referensi Utama dari CPMK
                'ref' => $c->referensis->map(fn ($ref) => [
                    'id' => $ref->id,
                    'kode' => $ref->kode,
                    'judul' => $ref->judul,
                    'penulis' => $ref->penulis,
                    'penerbit' => $ref->penerbit,
                    'tahun' => $ref->tahun,
                    'link' => $ref->link,
                ]),

                // CPL diambil dari relasi cpls() yang ada di Model CPMK
                'cpl' => $c->cpls->map(fn ($cpl) => [
                    'id' => $cpl->id,
                    'kode' => $cpl->kode,
                    'deskripsi' => $cpl->deskripsi,
                ]),

                'total_bobot' => $c->scpmks->sum('bobot'),
            ];
        })->toArray();

    }

    public function inputCPMKFilter()
    {
        $search = trim($this->cpmkSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->cpmk_name) {
            $this->cpmkSearchResults = $this->mapCPMK(
                CPMK::query()->with(['scpmks'])
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
        $data = CPMK::with(['scpmks'])->
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

        $query = CPMK::query()->with(['scpmks']);

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

        $query = CPMK::query()->with(['scpmks']);

        if (! $prodiId) {
            $defaultCPMK = $query
                ->latest()
                ->limit(12)
                ->get();

            return $this->mapCPMK($defaultCPMK);
        }

        $mainResults = $query
            ->whereHas('rps.matkul_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = CPMK::whereNotIn('id', $mainResults->pluck('id'))->with(['scpmks'])
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

    }

    public function selectCPMK($id, $cpmkName)
    {
        $this->cpmk_id = $id;
        $this->cpmkNameSearch = $cpmkName;
        $this->cpmkResults = $this->getCPMKbyUser();

        $data = CPMK::with(['scpmks'])->find($id);
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

            $mappedData = $this->mapCPMK(collect([$data]));
            $this->sub_items_array[] = $mappedData[0];
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
        $this->sub_items_array = [];
        $this->cpmkNameSearch = '';
    }
}
