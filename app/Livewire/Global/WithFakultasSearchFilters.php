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

    public $fk_id;

    public $fakultas_name = '';

    public $fakultas_items;

    public $fakultasNameSearch = '';

    public $fakultasResults = [];

    public $selectedFakultasId = null;

    private function mapFakultas($collection)
    {
        return $collection->map(fn ($fk) => [
            'id' => $fk->id,
            'kode' => $fk->kode,
            'fakultas' => $fk->fakultasFk
        ])->toArray();
    }
    
    private function fkQuery()
    {
        return Fakultas::query();
    }

    private function itemsFk($fk)
    {
        if (! $fk) {
            return null;
        }
        return [
            'id' => $fk->id,
            'kode' => $fk->kode,
            'name' => $fk->fakultasFk,
        ];
    }

    public function inputFakultasFilter()
    {
        $search = trim($this->fakultasSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->fakultas_name) {
            $this->fakultasSearchResults = $this->mapFakultas(
                $this->fkQuery()
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
        $this->reset(['selectedFakultasId', 'fakultasSearchQuery', 'fakultas_name', 'fakultas_items']);
        $this->resetPage();
    }

    public function selectFakultasForFilter($id)
    {
        $data = $this->fkQuery()->find($id);
        if ($data) {
            $this->selectedFakultasId = $id;
            $this->fakultas_name = $data->fakultasFk;
            $this->fakultasSearchQuery = $data->fakultasFk;
            $this->fakultas_items = $this->itemsFk($data);
            $this->fakultasSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedFakultasNameSearch($value)
    {
        $this->fk_id = null;
        $this->fakultas_items = null;
        $this->resetErrorBag(['fk_id', 'fakultasNameSearch']);

        $query = $this->fkQuery()
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
                $this->fk_id = $exactMatch->id;
                $this->fakultas_items = $this->itemsFk($exactMatch);
                $this->fakultasNameSearch = $exactMatch->fakultasFk;
                $this->fakultasResults = [];
            }

        } else {
            if (Auth::user()->fk_id) {
                $this->fakultasResults = $this->getFakultasbyUser();
            } else {
                $this->fakultasResults = $this->mapFakultas(
                    $query->orderBy('fakultas.nama_fk')->limit(12)->get()
                );
            }
        }
    }

    public function getFakultasbyUser()
    {
        $user = Auth::user();
        $fakultasId = $user->fk_id ?? null;

        $query = $this->fkQuery();

        if (! $fakultasId) {
            $defaultFakultas = $query
                ->orderBy('nama_fk', 'asc')
                ->limit(12)
                ->get();
            return $this->mapFakultas($defaultFakultas);
        }

        $mainResults = $query
            ->orderBy('nama_fk', 'asc')
            ->get()
            ->sortBy(fn ($f) => $f->id === $fakultasId ? 0 : 1)
            ->take(12);

        return $this->mapFakultas($mainResults);
    }

    public function fetchFakultas($query = '')
    {
        if (empty($query) || $this->fk_id) {
            $this->fakultasResults = $this->getFakultasbyUser();

            return;
        }
    }

    public function selectFakultas($id, $fakultasName)
    {
        $this->fk_id = $id;
        $this->fakultasNameSearch = $fakultasName;
        $this->fakultasResults = $this->getFakultasbyUser();

        $data = $this->fkQuery()->find($id);
        if ($data) {
            $this->fakultas_items = $this->itemsFk($data);
        }

        // if (property_exists($this, 'pr_id_array')) {
        //     $this->pr_id_array = [];
        //     $this->prodi_name_array = [];
        //     $this->prodi_items_array = [];
        //     $this->prodiNameSearch = '';
        // }

        $this->resetErrorBag(['fk_id', 'fakultasNameSearch']);
    }

    public function resetFakultasInput()
    {
        $this->fk_id = null;
        $this->fakultas_items = null;
        $this->fakultasNameSearch = '';

        // if (property_exists($this, 'pr_id_array')) {
        //     $this->pr_id_array = [];
        //     $this->prodi_name_array = [];
        //     $this->prodi_items_array = [];
        // }

        $this->updatedFakultasNameSearch('');
        $this->resetErrorBag(['fk_id', 'fakultasNameSearch']);
    }
}
