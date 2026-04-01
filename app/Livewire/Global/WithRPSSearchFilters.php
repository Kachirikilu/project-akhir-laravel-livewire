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
    public $rps_name = '';
    public $rps_id;
    public $rps_kode;
    public $rpsNameSearch = '';
    public $rpsResults = [];
    public $selectedRPSId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $rps_id_array = [];
    public $rps_name_array = [];
    public $rps_kode_array = [];

    /**
     * Helper untuk mapping hasil agar seragam
     */
    private function mapRPS($collection)
    {
        return $collection->map(fn ($mk) => [
            'id' => $mk->id,
            'mk_id' => $mk->mk_id,
            'kode' => $mk->kode,
            'matkul' => $mk->matkul,
            'tahun_akademik' => $mk->tahun_akademik,
            'draf_text' => $mk->draf_text,
            'tanggal_revisi' => $mk->tanggal_revisi,
            'wajib_text' => $mk->wajib_text,
        ])->toArray();
    }

    public function inputRPSFilter()
    {
        $search = trim($this->rpsSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->rps_name) {
            $this->rpsSearchResults = $this->mapRPS(
                RPS::searchRPS($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->rps_name) {
            $this->rpsSearchResults = $this->getRPSbyUser();
        } else {
            $this->rpsSearchResults = [];
        }
    }

    public function resetRPSFilter()
    {
        $this->reset(['selectedRPSId', 'rps_name', 'rpsSearchQuery', 'rps_kode']);
        $this->resetPage();
    }

    public function selectRPSForFilter($id)
    {
        $data = RPS::with(['matkul_rel'])->find($id);

        if ($data) {
            $this->selectedRPSId = $id;
            $this->rps_kode = $data->kode;
            $this->rps_name = $data->matkul;
            $this->rpsSearchQuery = $data->matkul;
            $this->rpsSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRPSNameSearch($value)
    {
        $this->rps_id = null;
        $this->rps_kode = null;
        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);

        $query = RPS::query();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchRPS($value)->limit(12)->get();
            $this->rpsResults = $this->mapRPS($results);

            // Exact Match Logic
            $exactMatch = $results->first(function ($r) use ($value) {
                return strtolower($r->matkul) === strtolower($value) 
                    || strtolower($r->kode) === strtolower($value);
            });

            if ($exactMatch) {
                $this->rps_id = $exactMatch->id;
                $this->rps_kode = $exactMatch->kode;
                $this->rpsNameSearch = $exactMatch->matkul;
                $this->rpsResults = [];
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

        $query = RPS::query();
        
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

    public function fetchRPS($query = '')
    {
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

        $data = RPS::find($id);
        if ($data) {
            $this->rps_kode = $data->kode;
        }

        if (method_exists($this, 'fetchRPS')) {
            $this->fetchRPS('');
        }

        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);
    }
    public function selectRPSArray($id)
    {
        $data = RPS::find($id);
        if ($data && ! in_array($id, $this->rps_id_array)) {
            $this->rps_id_array[] = $id;
            $this->rps_name_array[] = $data->matkul;
            $this->rps_kode_array[] = $data->kode;
        }
    }

    public function resetRPSInput()
    {
        $this->reset(['rps_id', 'rps_kode', 'rpsNameSearch']);
        $this->rpsResults = $this->getRPSbyUser();
    }

    public function resetRPSArray()
    {
        $this->rps_id_array = [];
        $this->rps_name_array = [];
        $this->rps_kode_array = [];
        $this->rpsNameSearch = '';
    }
}