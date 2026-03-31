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
    public $matkul_name = '';
    public $matkul_id;
    public $matkul_kode;
    public $matkulNameSearch = '';
    public $matkulResults = [];
    public $selectedMatkulId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $matkul_id_array = [];
    public $matkul_name_array = [];
    public $matkul_kode_array = [];

    /**
     * Helper untuk mapping hasil agar seragam
     */
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

    public function inputMatkulFilter()
    {
        $search = trim($this->matkulSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->matkul_name) {
            $this->matkulSearchResults = $this->mapMatkul(
                MataKuliah::searchMK($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->matkul_name) {
            $this->matkulSearchResults = $this->getMatkulbyUser();
        } else {
            $this->matkulSearchResults = [];
        }
    }

    public function resetMatkulFilter()
    {
        $this->reset(['selectedMatkulId', 'matkul_name', 'matkulSearchQuery', 'matkul_kode']);
        $this->resetPage();
    }

    public function selectMatkulForFilter($id)
    {
        $data = MataKuliah::
        // with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])->
        find($id);

        if ($data) {
            $this->selectedMatkulId = $id;
            $this->matkul_kode = $data->kode;
            $this->matkul_name = $data->matkul;
            $this->matkulSearchQuery = $data->matkul;
            $this->matkulSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedMatkulNameSearch($value)
    {
        $this->matkul_id = null;
        $this->matkul_kode = null;
        $this->resetErrorBag(['matkul_id', 'matkulNameSearch']);

        $query = MataKuliah::query();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchMK($value)->limit(12)->get();
            $this->matkulResults = $this->mapMatkul($results);

            // Exact Match Logic
            $exactMatch = $results->first(function ($mk) use ($value) {
                return strtolower($mk->matkul) === strtolower($value) 
                    || strtolower($mk->kode) === strtolower($value);
            });

            if ($exactMatch) {
                $this->matkul_id = $exactMatch->id;
                $this->matkul_kode = $exactMatch->kode;
                $this->matkulNameSearch = $exactMatch->matkul;
                $this->matkulResults = [];
            }
        } else {
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

        $query = MataKuliah::query();
        
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
            $extra = $query
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
                
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapMatkul($mainResults);
    }

    public function fetchMatkul($query = '')
    {
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

        $data = Matakuliah::find($id);
        if ($data) {
            $this->matkul_kode = $data->kode;
        }

        if (method_exists($this, 'fetchMatkul')) {
            $this->fetchMatkul('');
        }

        $this->resetErrorBag(['matkul_id', 'matkulNameSearch']);
    }

    public function resetMatkulInput()
    {
        $this->reset(['matkul_id', 'matkul_kode', 'matkulNameSearch']);
        $this->matkulResults = $this->getMatkulbyUser();
    }
}