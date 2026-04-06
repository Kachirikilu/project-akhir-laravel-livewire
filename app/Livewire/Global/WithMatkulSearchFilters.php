<?php

namespace App\Livewire\Global;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithMatkulSearchFilters
{
    use WithPagination;

    public $matkulSearchQuery = '';
    public $matkulSearchResults = [];
    public $modeMK = '';
    public $matkul_id;
    public $matkul_name = '';
    public $matkul_items;
    public $matkulNameSearch = '';
    public $matkulResults = [];
    public $selectedMatkulId = null;

    public $matkul_id_array = [];
    public $matkul_items_array = [];


    private function mapMatkul($collection)
    {
        return $collection->map(fn ($mk) => [
            'id' => $mk->id,
            'kode' => $mk->kode,
            'matkul' => $mk->matkul,
            'semester' => $mk->semester,
            'sks' => $mk->sks,
            'tipe_sks_text' => $mk->tipe_sks_text,
            'wajib_text' => $mk->wajib_text,
            'tingkatan_mk' => $mk->tingkatan_mk 
        ])->toArray();
    }

    private function mkQuery()
    {
        return MataKuliah::query()->with('prodis');
    }

    private function itemsMK($mk)
    {
        if (! $mk) {
            return null;
        }
        return [
            'kode' => $mk->kode,
            'name' => $mk->matkul,
        ];
    }

    public function inputMatkulFilter()
    {
        $search = trim($this->matkulSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->matkul_name) {
            $this->matkulSearchResults = $this->mapMatkul(
                $this->mkQuery()->searchMK($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->matkul_name) {
            $this->matkulSearchResults = $this->getMatkulbyUser();
        } else {
            $this->matkulSearchResults = [];
        }
    }

    public function resetMatkulFilter()
    {
        $this->reset(['selectedMatkulId', 'matkulSearchQuery', 'matkul_name', 'matkul_items']);
        $this->resetPage();
    }

    public function selectMatkulForFilter($id)
    {
        $data = $this->mkQuery()->find($id);

        if ($data) {
            $this->selectedMatkulId = $id;
            $this->matkul_name = $data->matkul;
            $this->matkulSearchQuery = $data->matkul;
            $this->matkul_items = $this->itemsMK($data);
            $this->matkulSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedMatkulNameSearch($value)
    {
        $this->matkul_id = null;
        $this->matkul_items = null;
        $this->resetErrorBag(['matkul_id', 'matkulNameSearch']);

        $query = $this->mkQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchMK($value)->limit(12)->get();
            $this->matkulResults = $this->mapMatkul($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($mk) use ($value, $normalizedValue) {
                $normalizedMkKode = str_replace(['-', ' '], '', strtolower($mk->kode));
                
                return strtolower($mk->matkul) === strtolower($value) 
                    || $normalizedMkKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->matkulNameSearch = $exactMatch->matkul;
                if ($this->modeMK == 'single') {
                    $this->matkul_id = $exactMatch->id;
                    $this->matkul_items = $this->itemsMK($exactMatch);
                    $this->matkulResults = [];
                } else {
                    $this->matkul_id_array[] = $exactMatch->id;
                    $this->matkul_items_array[] = $this->itemsMK($exactMatch);
                }
            }
        }else {
            if (Auth::user()->prodi_id) {
                $this->matkulResults = $this->getMatkulbyUser();
            } else {
                $this->matkulResults = $this->mapMatkul(
                    $query->orderBy('matkuls.nama_matkul')->limit(12)->get()
                );
            }
        }
    }

    public function getMatkulbyUser()
    {
        $user = Auth::user();
        $prodiId = $user->prodi_id ?? null;

        $query = $this->mkQuery();
        
        if (!$prodiId) {
            $defaultMatkul = $query
                ->latest()
                ->limit(12)
                ->get();
            return $this->mapMatkul($defaultMatkul);
        }

        $mainResults = $query
            ->whereHas('prodis', function($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->mkQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapMatkul($mainResults);
    }

    public function fetchMatkul($query = '', $mode = 'single')
    {
        $this->modeMK = $mode;
        if (empty($query) || $this->matkul_id) {
            $this->matkulResults = $this->getMatkulbyUser();
        }

        return;
    }


    public function selectMatkul($id, $matkulName)
    {
        $this->matkul_id = $id;
        $this->matkulNameSearch = $matkulName;
        $this->matkulResults = $this->getMatkulbyUser();

        $data = $this->mkQuery()->find($id);
        if ($data) {
            $this->matkul_items = $this->itemsMK($data);
        }

        if (method_exists($this, 'fetchMatkul')) {
            $this->fetchMatkul('');
        }

        $this->resetErrorBag(['matkul_id', 'matkulNameSearch']);
    }
    public function selectMatkulArray($id)
    {
        $data = $this->mkQuery()->find($id);

        if ($data && ! in_array($id, $this->matkul_id_array)) {
            $this->matkul_id_array[] = $id;
            $this->matkul_items_array[] = $this->itemsMK($data);
            $this->matkul_search = '';
        }
    }

    public function resetMatkulInput()
    {
        $this->reset(['matkul_id', 'matkul_items', 'matkulNameSearch']);
        $this->matkulResults = $this->getMatkulbyUser();
    }

    public function resetMatkulArray()
    {
        $this->matkul_id_array = [];
        $this->matkul_items_array = [];
        $this->matkulNameSearch = '';
    }
}