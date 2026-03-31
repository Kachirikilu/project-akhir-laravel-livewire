<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Fakultas;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithFakultasSearchFilters
{
    use WithPagination;

    public $fakultasSearchQuery = '';

    public $fakultasSearchResults = [];

    public $fakultas_name = '';

    public $fakultas_id;

    public $fakultas_kode;

    public $fakultasNameSearch = '';

    public $fakultasResults = [];

    public $selectedFakultasId = null;

    private function mapFakultas($collection)
    {
        return $collection->map(fn ($fk) => [
            'id' => $fk->id,
            'kode' => $fk->kode,
            'fakultas' => $fk->fakultas
        ])->toArray();
    }

    public function inputFakultasFilter()
    {
        $search = trim($this->fakultasSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->fakultas_name) {
            $this->fakultasSearchResults = $this->mapFakultas(
                Fakultas::query()
                    ->searchFakultas($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->fakultas_name) {
            $this->fakultasSearchResults = $this->getFakultasbyUser();
        } else {
            $this->fakultasSearchResults = [];
        }
    }

    public function resetFakultasFilter()
    {
        $this->reset(['selectedFakultasId', 'fakultas_name', 'fakultasSearchQuery', 'fakultas_kode']);
        $this->resetPage();
    }

    public function selectFakultasForFilter($id)
    {
        $data = Fakultas::find($id);
        if ($data) {
            $this->selectedFakultasId = $id;
            $this->fakultas_kode = $data->kode;
            $this->fakultas_name = 'Fakultas '.$data->fakultas;
            $this->fakultasSearchQuery = 'Fakultas '.$data->fakultas;
            $this->fakultasSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedFakultasNameSearch($value)
    {
        $this->fakultas_id = null;
        $this->fakultas_kode = null;
        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);

        $query = Fakultas::query()
            ->select('fakultas.*');

        if (trim(strlen($value)) > 0) {
            $results = $query->searchFakultas($value)->limit(12)->get();
            $this->fakultasResults = $this->mapFakultas($results);

            $exactMatch = $results->first(function ($fakultas) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($fakultas->fakultas)->lower();
                $kode = str($fakultas->kode)->lower();

                return $input->is([
                    $nama,
                    "fakultas $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->fakultas_id = $exactMatch->id;
                $this->fakultas_kode = $exactMatch->kode;
                $this->fakultasNameSearch = 'Fakultas '.$exactMatch->fakultas;
                $this->fakultasResults = [];
            }

        } else {
            if (Auth::user()->fakultas_id) {
                $this->fakultasResults = $this->getFakultasbyUser();
            } else {
                $this->fakultasResults = $this->mapFakultas(
                    $query->orderBy('fakultas.nama_fakultas')->limit(12)->get()
                );
            }
        }
    }

    public function getFakultasbyUser()
    {
        $user = Auth::user();
        $fakultasId = $user->fakultas_id ?? null;

        $query = Fakultas::query();

        if (! $fakultasId) {
            $defaultFakultas = $query
                ->orderBy('nama_fakultas', 'asc')
                ->limit(12)
                ->get();
            return $this->mapFakultas($defaultFakultas);
        }

        $mainResults = $query
            ->orderBy('nama_fakultas', 'asc')
            ->get()
            ->sortBy(fn ($f) => $f->id === $fakultasId ? 0 : 1)
            ->take(12);

        return $this->mapFakultas($mainResults);
    }

    public function fetchFakultas($query = '')
    {
        if (empty($query) || $this->fakultas_id) {
            $this->fakultasResults = $this->getFakultasbyUser();

            return;
        }
    }

    public function selectFakultas($id, $fakultasName)
    {
        $this->fakultas_id = $id;
        $this->fakultasNameSearch = 'Fakultas '.$fakultasName;
        $this->fakultasResults = $this->getFakultasbyUser();

        $data = Fakultas::find($id);
        if ($data) {
            $this->fakultas_kode = $data->kode;
        }

        if (property_exists($this, 'prodi_id_array')) {
            $this->prodi_id_array = [];
            $this->prodi_name_array = [];
            $this->prodi_kode_array = [];
            $this->prodiNameSearch = '';
        }

        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);
    }

    public function resetFakultasInput()
    {
        $this->fakultas_id = null;
        $this->fakultas_kode = null;
        $this->fakultasNameSearch = '';

        if (property_exists($this, 'prodi_id_array')) {
            $this->prodi_id_array = [];
            $this->prodi_name_array = [];
            $this->prodi_kode_array = [];
        }

        $this->updatedFakultasNameSearch('');
        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);
    }
}
