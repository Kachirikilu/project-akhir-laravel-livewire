<?php

namespace App\Livewire\Global;

use App\Models\Akademik\Referensi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithReferensiSearchFilters
{
    use WithPagination;

    public $refSearchQuery = '';
    public $refSearchResults = [];
    public $ref_name = '';
    public $ref_id;
    public $ref_kode;
    public $refNameSearch = '';
    public $refResults = [];
    public $selectedRefId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $ref_id_array = [];
    public $ref_name_array = [];
    public $ref_kode_array = [];
    // public $ref_item_array = [];

    /**
     * Helper untuk mapping hasil agar seragam
     */
    private function mapRef($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'judul' => $c->judul,
            'penulis' => $c->penulis,
            'penerbit' => $c->penerbit,
            'tahun' => $c->tahun,
            'link' => $c->link,
        ])->toArray();
    }

    public function inputRefFilter()
    {
        $search = trim($this->refSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->ref_name) {
            $this->refSearchResults = $this->mapRef(
                Referensi::searchRef($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->ref_name) {
            $this->refSearchResults = $this->getRefbyUser();
        } else {
            $this->refSearchResults = [];
        }
    }

    public function resetRefFilter()
    {
        $this->reset(['selectedRefId', 'ref_name', 'refSearchQuery', 'ref_kode']);
        $this->resetPage();
    }

    public function selectRefForFilter($id)
    {
        $data = Referensi::
        // with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])->
        find($id);

        if ($data) {
            $this->selectedRefId = $id;
            $this->ref_kode = $data->kode;
            $this->ref_name = $data->judul;
            $this->refSearchQuery = $data->judul;
            $this->refSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRefNameSearch($value)
    {
        $this->ref_id = null;
        $this->ref_kode = null;
        $this->resetErrorBag(['ref_id', 'refNameSearch']);

        $query = Referensi::query();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchRef($value)->limit(12)->get();
            $this->refResults = $this->mapRef($results);

            // Exact Match Logic
            $exactMatch = $results->first(function ($r) use ($value) {
                return strtolower($r->judul) === strtolower($value) 
                    || strtolower($r->kode) === strtolower($value);
            });

            if ($exactMatch) {
                $this->ref_id = $exactMatch->id;
                $this->ref_kode = $exactMatch->kode;
                $this->refNameSearch = $exactMatch->judul;
                $this->refResults = [];
            }
        } else {
            if (Auth::user()->prodi_id) {
                $this->refResults = $this->getRefbyUser();
            } else {
                $this->refResults = $this->mapRef(
                    $query->orderBy('referensis.judul')->limit(12)->get()
                );
            }
        }
    }

    public function getRefbyUser()
    {
        $user = Auth::user();
        $prodiId = $user->prodi_id ?? null;

        $query = Referensi::query();
        
        if (!$prodiId) {
            $defaultRef = $query
                ->latest()
                ->limit(12)
                ->get();
            return $this->mapRef($defaultRef);
        }

        $mainResults = $query->where(function ($q) use ($prodiId) {
            $q->whereRelation('scpmks.cpmks.rps.matkul_rel.prodis', 'prodis.id', $prodiId)
            ->orWhereRelation('cpmks.rps.matkul_rel.prodis', 'prodis.id', $prodiId)
            ->orWhereRelation('rps.matkul_rel.prodis', 'prodis.id', $prodiId);
        })->limit(12)->get();
        

        if ($mainResults->count() < 12) {
            $extra = Referensi::whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapRef($mainResults);
    }

    public function fetchRef($query = '')
    {
        if (empty($query) || $this->ref_id) {
            $this->refResults = $this->getRefbyUser();
        }

        return;
    }


    public function selectRef($id, $refName)
    {
        $this->ref_id = $id;
        $this->refNameSearch = $refName;
        $this->refResults = $this->getRefbyUser();

        $data = Referensi::find($id);
        if ($data) {
            $this->ref_kode = $data->kode;
        }

        if (method_exists($this, 'fetchRef')) {
            $this->fetchRef('');
        }

        $this->resetErrorBag(['ref_id', 'refNameSearch']);
    }
    public function selectRefArray($id)
    {
        $data = Referensi::find($id);
        if ($data && ! in_array($id, $this->ref_id_array)) {
            $this->ref_id_array[] = $id;
            $this->ref_name_array[] = $data->judul;
            $this->ref_kode_array[] = $data->kode;
            // $this->ref_item_array[] = [
            //     'penulis' => $data->penulis,
            //     'penerbit' => $data->penerbit,
            //     'tahun' => $data->tahun,
            //     'link' => $data->link
            // ];
        }
    }

    public function resetRefInput()
    {
        $this->reset(['ref_id', 'ref_kode', 'refNameSearch']);
        $this->refResults = $this->getRefbyUser();
    }

    public function resetRefArray()
    {
        $this->ref_id_array = [];
        $this->ref_name_array = [];
        $this->ref_kode_array = [];
        // $this->ref_item_array = [];
        $this->refNameSearch = '';
    }
}