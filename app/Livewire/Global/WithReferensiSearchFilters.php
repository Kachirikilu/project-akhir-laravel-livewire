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
    public $modeRef = '';
    public $ref_id;
    public $ref_name = '';
    public $ref_items;
    public $refNameSearch = '';
    public $refResults = [];
    public $selectedRefId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $ref_id_array = [];
    public $ref_name_array = [];
    public $ref_items_array = [];
    // public $ref_item_array = [];

    private function mapRef($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'judul' => $c->judul,
            'penulis' => $c->penulis,
            'penulis_tahun' => $c->penulis_tahun,
            'penerbit' => $c->penerbit,
            'tahun' => $c->tahun,
            'link' => $c->link,
        ])->toArray();
    }

    private function refQuery()
    {
        return Referensi::query()->with('rps', 'cpmks.rps', 'scpmks.cpmks.rps',
                                        'cpmks', 'scpmks.cpmks',
                                        'scpmks');
    }

    private function itemsRef($r)
    {
        if (! $r) {
            return null;
        }
        return [
            'id' => $r->id,
            'kode' => $r->kode,
            'name' => $r->judul,
            'name2' => $r->penulis_tahun,
            'name3' => $r->penerbit,
        ];
    }

    public function inputRefFilter()
    {
        $search = trim($this->refSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->ref_name) {
            $this->refSearchResults = $this->mapRef(
                $this->refQuery()->searchRef($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->ref_name) {
            $this->refSearchResults = $this->getRefbyUser();
        } else {
            $this->refSearchResults = [];
        }
    }

    public function resetRefFilter()
    {
        $this->reset(['selectedRefId', 'refSearchQuery', 'ref_name', 'ref_items']);
        $this->resetPage();
    }

    public function selectRefForFilter($id)
    {
        $data = $this->refQuery()->find($id);

        if ($data) {
            $this->selectedRefId = $id;
            $this->ref_name = $data->judul;
            $this->refSearchQuery = $data->judul;
            $this->ref_items = $this->itemsRef($data);
            $this->refSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRefNameSearch($value)
    {
        $this->ref_id = null;
        $this->ref_items = null;
        $this->resetErrorBag(['ref_id', 'refNameSearch']);

        $query = $this->refQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchRef($value)->limit(12)->get();
            $this->refResults = $this->mapRef($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($r) use ($value, $normalizedValue) {
                $normalizedMkKode = str_replace(['-', ' '], '', strtolower($r->kode));
                
                return strtolower($r->judul) === strtolower($value) 
                    || $normalizedMkKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->refNameSearch = $exactMatch->judul;
                if ($this->modeRef == 'single') {
                    $this->ref_id = $exactMatch->id;
                    $this->ref_items = $this->itemsRef($exactMatch);
                    $this->refResults = [];
                } else {
                    $this->ref_id_array[] = $exactMatch->id;
                    $this->ref_items_array[] = $this->itemsRef($exactMatch);
                }
            }
        } else {
            if (Auth::user()->pr_id) {
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
        $prodiId = $user->pr_id ?? null;

        $query = $this->refQuery();
        
        if (!$prodiId) {
            $defaultRef = $query
                ->latest()
                ->limit(12)
                ->get();
            return $this->mapRef($defaultRef);
        }

        $mainResults = $query->where(function ($q) use ($prodiId) {
            $q->whereRelation('scpmks.cpmks.rps.mk_rel.prodis', 'prodis.id', $prodiId)
            ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $prodiId)
            ->orWhereRelation('rps.mk_rel.prodis', 'prodis.id', $prodiId);
        })->limit(12)->get();
        

        if ($mainResults->count() < 12) {
            $extra = $this->refQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapRef($mainResults);
    }

    public function fetchRef($query = '', $mode = 'single')
    {
        $this->modeRef = $mode;
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

        $data = $this->refQuery()->find($id);
        if ($data) {
            $this->ref_items = $this->itemsRef($data);
        }

        if (method_exists($this, 'fetchRef')) {
            $this->fetchRef('');
        }

        $this->resetErrorBag(['ref_id', 'refNameSearch']);
    }
    public function selectRefArray($id)
    {
        $data = $this->refQuery()->find($id);
        if ($data && ! in_array($id, $this->ref_id_array)) {
            $this->ref_id_array[] = $id;
            $this->ref_name_array[] = $data->judul;
            $this->ref_items_array[] = $data->kode;
            $this->ref_search = '';
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
        $this->reset(['ref_id', 'ref_items', 'refNameSearch']);
        $this->refResults = $this->getRefbyUser();
    }

    public function resetRefArray()
    {
        $this->ref_id_array = [];
        $this->ref_name_array = [];
        $this->ref_items_array = [];
        // $this->ref_item_array = [];
        $this->refNameSearch = '';
    }
}