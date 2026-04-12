<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Jurusan;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithJurusanSearchFilters
{
    use WithPagination;

    public $jrSearchQuery = '';

    public $jrSearchResults = [];

    public $jr_id;

    public $jr_name = '';

    public $jr_items;

    public $jrNameSearch = '';

    public $jrResults = [];

    public $selectedJrId = null;

    private function mapJr($collection)
    {
        return $collection->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'jurusan' => $j->jurusanJr,
            'fakultas' => $j->fakultasFk
        ])->toArray();
    }

    private function jrQuery()
    {
        return Jurusan::query()->with('fk_rel');
    }

    private function itemsJr($j)
    {
        if (! $j) {
            return null;
        }
        return [
            'id' => $j->id,
            'kode' => $j->kode,
            'slot1' => $j->jurusanJr,
            'slot2' => $j->fakultasFk,
        ];
    }

    public function inputJrFilter()
    {
        $search = trim($this->jrSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->jr_name) {
            $this->jrSearchResults = $this->mapJr(
                $this->jrQuery()
                    ->searchJurusan($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->jr_name) {
            $this->jrSearchResults = $this->getJrbyUser();
        } else {
            $this->jrSearchResults = [];
        }
    }

    public function resetJrFilter()
    {
        $this->reset(['selectedJrId', 'jrSearchQuery', 'jr_name', 'jr_items']);
        $this->resetPage();
    }

    public function selectJrForFilter($id)
    {
        $data = $this->jrQuery()->find($id);

        if ($data) {
            $this->selectedJrId = $id;
            $this->jr_name = $data->jurusanJr;
            $this->jrSearchQuery = $data->jurusanJr;
            $this->jr_items = $this->itemsJr($data);
            $this->jrSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedJrNameSearch($value)
    {
        $this->jr_id = null;
        $this->jr_items = null;
        $this->resetErrorBag(['jr_id', 'jrNameSearch']);

        $query = $this->jrQuery()->select('jurusans.*');

        if (trim(strlen($value)) > 0) {

            $results = $query->searchJurusan($value)->limit(12)->get();
            $this->jrResults = $this->mapJr($results);

            $exactMatch = $results->first(function ($jurusan) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($jurusan->jurusan)->lower();
                $kode = str($jurusan->kode)->lower();

                return $input->is([
                    $nama,
                    "jurusan $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->jr_id = $exactMatch->id;
                $this->jr_items = $this->itemsJr($exactMatch);
                $this->jrNameSearch = $exactMatch->jurusanJr;
                $this->jrResults = [];
            }

        } else {
            if (Auth::user()->jr_id) {
                $this->jrResults = $this->getJrbyUser();
            } else {
                $this->jrResults = $this->mapJr(
                    $query->orderBy('jurusans.nama_jr')->limit(12)->get()
                );
            }
        }
    }

    public function getJrbyUser()
    {
        $user = Auth::user();
        $jurusanId = $user->jr_id ?? null;
        $fakultasId = $user->fk_id ?? null;

        $query = $this->jrQuery();

        if (! $jurusanId) {
            $defaultJurusans = $this->jrQuery()
                    ->orderBy('nama_jr', 'asc')
                    ->limit(12)
                    ->get();
            return $this->mapJr($defaultJurusans);
        }

        $mainResults = $query
            ->where('fk_id', $fakultasId)
            ->get()
            ->sortBy(fn ($j) => $j->id === $jurusanId ? 0 : 1)
            ->take(12);

        if ($mainResults->count() < 12) {
            $extra = $this->jrQuery()
                ->whereHas('fk_rel', fn ($q) => $q->where('id', '!=', $fakultasId))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapJr($mainResults);
    }

    public function fetchJr($query = '')
    {
        if (empty($query) || $this->jr_id) {
            $this->jrResults = $this->getJrbyUser();
            return;
        }
    }

    public function selectJr($id, $jurusanName)
    {
        $this->jr_id = $id;
        $this->jrNameSearch = $jurusanName;
        $this->jrResults = $this->getJrbyUser();

        $data = $this->jrQuery()->find($id);
        if ($data) {
            $this->jr_items = $this->itemsJr($data);
        }

        if (property_exists($this, 'pr_id_array') && property_exists($this, 'mkType')) {
            if ($this->mkType == 2 || $this->mkType == 3) {
                $this->resetPrArray();
            }
        }

        if (method_exists($this, 'fetchJr')) {
            $this->fetchJr('');
        }

        $this->resetErrorBag(['jr_id', 'jrNameSearch']);
    }

    public function resetJrInput()
    {
        $this->jr_id = null;
        $this->jr_items = null;
        $this->jrNameSearch = '';

        if (property_exists($this, 'pr_id_array') && property_exists($this, 'mkType')) {
            if ($this->mkType == 2 || $this->mkType == 3) {
                $this->resetPrArray();
            }
        }

        $this->updatedJrNameSearch('');
        $this->resetErrorBag(['jr_id', 'jrNameSearch']);
    }
}
