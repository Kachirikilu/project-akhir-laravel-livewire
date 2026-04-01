<?php

namespace App\Livewire\Global;

use App\Models\Akademik\CPL;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithCPLSearchFilters
{
    use WithPagination;

    public $cplSearchQuery = '';
    public $cplSearchResults = [];
    public $cpl_name = '';
    public $cpl_id;
    public $cpl_kode;
    public $cplNameSearch = '';
    public $cplResults = [];
    public $selectedCPLId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $cpl_id_array = [];
    public $cpl_name_array = [];
    public $cpl_kode_array = [];

    /**
     * Helper untuk mapping hasil agar seragam
     */
    private function mapCPL($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'deskripsi' => $c->deskripsi,
        ])->toArray();
    }

    public function inputCPLFilter()
    {
        $search = trim($this->cplSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->cpl_name) {
            $this->cplSearchResults = $this->mapCPL(
                CPL::searchCPL($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->cpl_name) {
            $this->cplSearchResults = $this->getCPLbyUser();
        } else {
            $this->cplSearchResults = [];
        }
    }

    public function resetCPLFilter()
    {
        $this->reset(['selectedCPLId', 'cpl_name', 'cplSearchQuery', 'cpl_kode']);
        $this->resetPage();
    }

    public function selectCPLForFilter($id)
    {
        $data = CPL::
        // with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])->
        find($id);

        if ($data) {
            $this->selectedCPLId = $id;
            $this->cpl_kode = $data->kode;
            $this->cpl_name = $data->deskripsi;
            $this->cplSearchQuery = $data->deskripsi;
            $this->cplSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedCPLNameSearch($value)
    {
        $this->cpl_id = null;
        $this->cpl_kode = null;
        $this->resetErrorBag(['cpl_id', 'cplNameSearch']);

        $query = CPL::query();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchCPL($value)->limit(12)->get();
            $this->cplResults = $this->mapCPL($results);

            // Exact Match Logic
            $exactMatch = $results->first(function ($mk) use ($value) {
                return strtolower($mk->deskripsi) === strtolower($value) 
                    || strtolower($mk->kode) === strtolower($value);
            });

            if ($exactMatch) {
                $this->cpl_id = $exactMatch->id;
                $this->cpl_kode = $exactMatch->kode;
                $this->cplNameSearch = $exactMatch->deskripsi;
                $this->cplResults = [];
            }
        } else {
            if (Auth::user()->prodi_id) {
                $this->cplResults = $this->getCPLbyUser();
            } else {
                $this->cplResults = $this->mapCPL(
                    $query->orderBy('cpls.deskripsi')->limit(12)->get()
                );
            }
        }
    }

    public function getCPLbyUser()
    {
        $user = Auth::user();
        $prodiId = $user->prodi_id ?? null;

        $query = CPL::query();
        
        if (!$prodiId) {
            $defaultCPL = $query
                ->latest()
                ->limit(12)
                ->get();
            return $this->mapCPL($defaultCPL);
        }

        $mainResults = $query
            ->whereHas('cpmks.rps.matkul_rel.prodis', function($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = CPL::whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapCPL($mainResults);
    }

    public function fetchCPL($query = '')
    {
        if (empty($query) || $this->cpl_id) {
            $this->cplResults = $this->getCPLbyUser();
        }

        return;
    }


    public function selectCPL($id, $cplName)
    {
        $this->cpl_id = $id;
        $this->cplNameSearch = $cplName;
        $this->cplResults = $this->getCPLbyUser();

        $data = CPL::find($id);
        if ($data) {
            $this->cpl_kode = $data->kode;
        }

        if (method_exists($this, 'fetchCPL')) {
            $this->fetchCPL('');
        }

        $this->resetErrorBag(['cpl_id', 'cplNameSearch']);
    }
    public function selectCPLArray($id)
    {
        $data = CPL::find($id);
        if ($data && ! in_array($id, $this->cpl_id_array)) {
            $this->cpl_id_array[] = $id;
            $this->cpl_name_array[] = $data->deskripsi;
            $this->cpl_kode_array[] = $data->kode;
        }
    }

    public function resetCPLInput()
    {
        $this->reset(['cpl_id', 'cpl_kode', 'cplNameSearch']);
        $this->cplResults = $this->getCPLbyUser();
    }

    public function resetCPLArray()
    {
        $this->cpl_id_array = [];
        $this->cpl_name_array = [];
        $this->cpl_kode_array = [];
        $this->cplNameSearch = '';
    }
}