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

    private function mapJurusan($collection)
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

    public function inputJurusanFilter()
    {
        $search = trim($this->jrSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->jr_name) {
            $this->jrSearchResults = $this->mapJurusan(
                $this->jrQuery()
                    ->searchJurusan($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->jr_name) {
            $this->jrSearchResults = $this->getJurusanbyUser();
        } else {
            $this->jrSearchResults = [];
        }
    }

    public function resetJurusanFilter()
    {
        $this->reset(['selectedJrId', 'jrSearchQuery', 'jr_name', 'jr_items']);
        $this->resetPage();
    }

    public function selectJurusanForFilter($id)
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

    public function updatedJurusanNameSearch($value)
    {
        $this->jr_id = null;
        $this->jr_items = null;
        $this->resetErrorBag(['jr_id', 'jrNameSearch']);

        $query = $this->jrQuery()->select('jurusans.*');

        if (trim(strlen($value)) > 0) {

            $results = $query->searchJurusan($value)->limit(12)->get();
            $this->jrResults = $this->mapJurusan($results);

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
                $this->jrResults = $this->getJurusanbyUser();
            } else {
                $this->jrResults = $this->mapJurusan(
                    $query->orderBy('jurusans.nama_jr')->limit(12)->get()
                );
            }
        }
    }

    public function getJurusanbyUser()
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
            return $this->mapJurusan($defaultJurusans);
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

        return $this->mapJurusan($mainResults);
    }

    public function fetchJurusan($query = '')
    {
        if (empty($query) || $this->jr_id) {
            $this->jrResults = $this->getJurusanbyUser();

            return;
        }
    }

    public function selectJurusan($id, $jurusanName)
    {
        $this->jr_id = $id;
        $this->jrNameSearch = $jurusanName;
        $this->jrResults = $this->getJurusanbyUser();

        $data = $this->jrQuery()->find($id);
        if ($data) {
            $this->jr_items = $this->itemsJr($data);
        }

        if (method_exists($this, 'fetchJurusan')) {
            $this->fetchJurusan('');
        }

        $this->resetErrorBag(['jr_id', 'jrNameSearch']);
    }

    public function resetJurusanInput()
    {
        $this->jr_id = null;
        $this->jr_items = null;
        $this->jrNameSearch = '';

        $this->updatedJurusanNameSearch('');
        $this->resetErrorBag(['jr_id', 'jrNameSearch']);
    }
}
