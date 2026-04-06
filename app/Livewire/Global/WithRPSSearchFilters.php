<?php

namespace App\Livewire\Global;

use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithRPSSearchFilters
{
    use WithPagination;

    public $rpsSearchQuery = '';
    public $rpsSearchResults = [];
    public $modeRPS = '';
    public $rps_id;
    public $rps_name = '';
    public $rps_items;
    public $rpsNameSearch = '';
    public $rpsResults = [];
    public $selectedRPSId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $rps_id_array = [];
    public $rps_name_array = [];
    public $rps_items_array = [];

    private function mapRPS($collection)
    {
        return $collection->map(fn ($mk) => [
            'id' => $mk->id,
            'mk_id' => $mk->mk_id,
            'kode' => $mk->kode,
            'rps' => $mk->rps,
            'matkul' => $mk->matkul,
            'akademik' => $mk->akademik,
            'draf_text' => $mk->draf_text,
            'tanggal_revisi' => $mk->tanggal_revisi,
            'wajib_text' => $mk->wajib_text,
        ])->toArray();
    }

    private function rpsQuery()
    {
        return RPS::query()->with(['matkul_rel', 'cpmks', 'cpmks.scpmks']);
    }

    private function itemsRPS($r)
    {
        if (! $r) {
            return null;
        }
        return [
            'kode' => $r->kode,
            'name' => $r->rps,
        ];
    }

    public function inputRPSFilter()
    {
        $search = trim($this->rpsSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->rps_name) {
            $this->rpsSearchResults = $this->mapRPS(
                $this->rpsQuery()->searchRPS($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->rps_name) {
            $this->rpsSearchResults = $this->getRPSbyUser();
        } else {
            $this->rpsSearchResults = [];
        }
    }

    public function resetRPSFilter()
    {
        $this->reset(['selectedRPSId', 'rpsSearchQuery', 'rps_name', 'rps_items']);
        $this->resetPage();
    }

    public function selectRPSForFilter($id)
    {
        $data = $this->rpsQuery()->with(['matkul_rel'])->find($id);

        if ($data) {
            $this->selectedRPSId = $id;
            $this->rps_name = $data->rps;
            $this->rpsSearchQuery = $data->rps;
            $this->rps_items = $this->itemsRPS($data);
            $this->rpsSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRPSNameSearch($value)
    {
        $this->rps_id = null;
        $this->rps_items = null;
        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);

        $query = $this->rpsQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchRPS($value)->limit(12)->get();
            $this->rpsResults = $this->mapRPS($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($r) use ($value, $normalizedValue) {
                $normalizedMkKode = str_replace(['-', ' '], '', strtolower($r->kode));
                
                return strtolower($r->rps) === strtolower($value) 
                    || strtolower($r->matkul) === strtolower($value)
                    || $normalizedMkKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->rpsNameSearch = $exactMatch->rps;
                if ($this->modeRPS == 'single') {
                    $this->rps_id = $exactMatch->id;
                    $this->rps_items = $this->itemsRPS($exactMatch);
                    $this->rpsResults = [];
                } else {
                    $this->rps_id_array[] = $exactMatch->id;
                    $this->rps_items_array[] = $this->itemsRPS($exactMatch);
                }
            }
        } else {
            if (Auth::user()->prodi_id) {
                $this->rpsResults = $this->getRPSbyUser();
            } else {
                $this->rpsResults = $this->mapRPS(
                    $query->orderBy('rps.matkul_rel.nama_matkul')->limit(12)->get()
                );
            }
        }
    }

    public function getRPSbyUser()
    {
        $user = Auth::user();
        $prodiId = $user->prodi_id ?? null;

        $query = $this->rpsQuery();
        
        if (!$prodiId) {
            $defaultRPS = $query
                ->latest()
                ->limit(12)
                ->get();
            return $this->mapRPS($defaultRPS);
        }

        $mainResults = $query
            ->whereHas('matkul_rel.prodis', function($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = RPS::whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapRPS($mainResults);
    }

    public function fetchRPS($query = '', $mode = 'single')
    {
        $this->modeRPS = $mode;
        if (empty($query) || $this->rps_id) {
            $this->rpsResults = $this->getRPSbyUser();
        }

        return;
    }


    public function selectRPS($id, $rpsName)
    {
        $this->rps_id = $id;
        $this->rpsNameSearch = $rpsName;
        $this->rpsResults = $this->getRPSbyUser();

        $data = $this->rpsQuery()->find($id);
        if ($data) {
            $this->rps_items = $this->itemsRPS($data);
        }

        if (method_exists($this, 'fetchRPS')) {
            $this->fetchRPS('');
        }

        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);
    }
    public function selectRPSArray($id)
    {
        $data = $this->rpsQuery()->find($id);
        if ($data && ! in_array($id, $this->rps_id_array)) {
            $this->rps_id_array[] = $id;
            $this->rps_name_array[] = $data->rps;
            $this->rps_items_array[] = $this->itemsRPS($data);
            $this->rps_search = '';
        }
    }

    public function resetRPSInput()
    {
        $this->reset(['rps_id', 'rps_items', 'rpsNameSearch']);
        $this->rpsResults = $this->getRPSbyUser();
    }

    public function resetRPSArray()
    {
        $this->rps_id_array = [];
        $this->rps_name_array = [];
        $this->rps_items_array = [];
        $this->rpsNameSearch = '';
    }
}