<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Fakultas;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithFakultasSearchFilters
{
    use WithPagination;

    public $fkSearchQuery = '';

    public $fkSearchResults = [];

    public $fk_id;

    public $fk_name = '';

    public $fk_items;

    public $fkNameSearch = '';

    public $fkResults = [];

    public $selectedFkId = null;

    private function mapFakultas($collection)
    {
        return $collection->map(fn ($f) => [
            'id' => $f->id,
            'kode' => $f->kode,
            'fakultas' => $f->fakultasFk
        ])->toArray();
    }
    
    private function fkQuery()
    {
        return Fakultas::query();
    }

    private function itemsFk($f)
    {
        if (! $f) {
            return null;
        }
        return [
            'id' => $f->id,
            'kode' => $f->kode,
            'slot1' => $f->fakultasFk,
        ];
    }

    public function inputFakultasFilter()
    {
        $search = trim($this->fkSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->fk_name) {
            $this->fkSearchResults = $this->mapFakultas(
                $this->fkQuery()
                    ->searchFakultas($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->fk_name) {
            $this->fkSearchResults = $this->getFakultasbyUser();
        } else {
            $this->fkSearchResults = [];
        }
    }

    public function resetFakultasFilter()
    {
        $this->reset(['selectedFkId', 'fkSearchQuery', 'fk_name', 'fk_items']);
        $this->resetPage();
    }

    public function selectFakultasForFilter($id)
    {
        $data = $this->fkQuery()->find($id);
        if ($data) {
            $this->selectedFkId = $id;
            $this->fk_name = $data->fakultasFk;
            $this->fkSearchQuery = $data->fakultasFk;
            $this->fk_items = $this->itemsFk($data);
            $this->fkSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedFakultasNameSearch($value)
    {
        $this->fk_id = null;
        $this->fk_items = null;
        $this->resetErrorBag(['fk_id', 'fkNameSearch']);

        $query = $this->fkQuery()
            ->select('fakultas.*');

        if (trim(strlen($value)) > 0) {
            $results = $query->searchFakultas($value)->limit(12)->get();
            $this->fkResults = $this->mapFakultas($results);

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
                $this->fk_items = $this->itemsFk($exactMatch);
                $this->fkNameSearch = $exactMatch->fakultasFk;
                $this->fkResults = [];
            }

        } else {
            if (Auth::user()->fk_id) {
                $this->fkResults = $this->getFakultasbyUser();
            } else {
                $this->fkResults = $this->mapFakultas(
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
            $this->fkResults = $this->getFakultasbyUser();

            return;
        }
    }

    public function selectFakultas($id, $fakultasName)
    {
        $this->fk_id = $id;
        $this->fkNameSearch = $fakultasName;
        $this->fkResults = $this->getFakultasbyUser();

        $data = $this->fkQuery()->find($id);
        if ($data) {
            $this->fk_items = $this->itemsFk($data);
        }

        // if (property_exists($this, 'pr_id_array')) {
        //     $this->pr_id_array = [];
        //     $this->pr_name_array = [];
        //     $this->pr_items_array = [];
        //     $this->prNameSearch = '';
        // }

        $this->resetErrorBag(['fk_id', 'fkNameSearch']);
    }

    public function resetFakultasInput()
    {
        $this->fk_id = null;
        $this->fk_items = null;
        $this->fkNameSearch = '';

        // if (property_exists($this, 'pr_id_array')) {
        //     $this->pr_id_array = [];
        //     $this->pr_name_array = [];
        //     $this->pr_items_array = [];
        // }

        $this->updatedFakultasNameSearch('');
        $this->resetErrorBag(['fk_id', 'fkNameSearch']);
    }
}
