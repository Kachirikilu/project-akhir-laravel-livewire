<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Jurusan;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithJurusanSearchFilters
{
    use WithPagination;

    public $jurusanSearchQuery = '';

    public $jurusanSearchResults = [];

    public $jurusan_id;

    public $jurusan_name = '';

    public $jurusan_items;

    public $jurusanNameSearch = '';

    public $jurusanResults = [];

    public $selectedJurusanId = null;

    private function mapJurusan($collection)
    {
        return $collection->map(fn ($jr) => [
            'id' => $jr->id,
            'kode' => $jr->kode,
            'jurusan' => $jr->jurusanJr,
            'fakultas' => $jr->fakultasFk
        ])->toArray();
    }

    private function jrQuery()
    {
        return Jurusan::query()->with('fakultas_rel');
    }

    private function itemsJr($jr)
    {
        if (! $jr) {
            return null;
        }
        return [
            'id' => $jr->id,
            'kode' => $jr->kode,
            'name' => $jr->jurusanJr,
            'name2' => $jr->fakultasFk,
        ];
    }

    public function inputJurusanFilter()
    {
        $search = trim($this->jurusanSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->jurusan_name) {
            $this->jurusanSearchResults = $this->mapJurusan(
                $this->jrQuery()
                    ->searchJurusan($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->jurusan_name) {
            $this->jurusanSearchResults = $this->getJurusanbyUser();
        } else {
            $this->jurusanSearchResults = [];
        }
    }

    public function resetJurusanFilter()
    {
        $this->reset(['selectedJurusanId', 'jurusanSearchQuery', 'jurusan_name', 'jurusan_items']);
        $this->resetPage();
    }

    public function selectJurusanForFilter($id)
    {
        $data = $this->jrQuery()->find($id);

        if ($data) {
            $this->selectedJurusanId = $id;
            $this->jurusan_name = $data->jurusanJr;
            $this->jurusanSearchQuery = $data->jurusanJr;
            $this->jurusan_items = $this->itemsJr($data);
            $this->jurusanSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedJurusanNameSearch($value)
    {
        $this->jurusan_id = null;
        $this->jurusan_items = null;
        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);

        $query = $this->jrQuery()->select('jurusans.*');

        if (trim(strlen($value)) > 0) {

            $results = $query->searchJurusan($value)->limit(12)->get();
            $this->jurusanResults = $this->mapJurusan($results);

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
                $this->jurusan_id = $exactMatch->id;
                $this->jurusan_items = $this->itemsJr($exactMatch);
                $this->jurusanNameSearch = $exactMatch->jurusanJr;
                $this->jurusanResults = [];
            }

        } else {
            if (Auth::user()->jurusan_id) {
                $this->jurusanResults = $this->getJurusanbyUser();
            } else {
                $this->jurusanResults = $this->mapJurusan(
                    $query->orderBy('jurusans.nama_jurusan')->limit(12)->get()
                );
            }
        }
    }

    public function getJurusanbyUser()
    {
        $user = Auth::user();
        $jurusanId = $user->jurusan_id ?? null;
        $fakultasId = $user->fakultas_id ?? null;

        $query = $this->jrQuery();

        if (! $jurusanId) {
            $defaultJurusans = $this->jrQuery()
                    ->orderBy('nama_jurusan', 'asc')
                    ->limit(12)
                    ->get();
            return $this->mapJurusan($defaultJurusans);
        }

        $mainResults = $query
            ->where('fakultas_id', $fakultasId)
            ->get()
            ->sortBy(fn ($j) => $j->id === $jurusanId ? 0 : 1)
            ->take(12);

        if ($mainResults->count() < 12) {
            $extra = $this->jrQuery()
                ->whereHas('fakultas_rel', fn ($q) => $q->where('id', '!=', $fakultasId))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();
            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapJurusan($mainResults);
    }

    public function fetchJurusan($query = '')
    {
        if (empty($query) || $this->jurusan_id) {
            $this->jurusanResults = $this->getJurusanbyUser();

            return;
        }
    }

    public function selectJurusan($id, $jurusanName)
    {
        $this->jurusan_id = $id;
        $this->jurusanNameSearch = $jurusanName;
        $this->jurusanResults = $this->getJurusanbyUser();

        $data = $this->jrQuery()->find($id);
        if ($data) {
            $this->jurusan_items = $this->itemsJr($data);
        }

        if (method_exists($this, 'fetchJurusan')) {
            $this->fetchJurusan('');
        }

        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);
    }

    public function resetJurusanInput()
    {
        $this->jurusan_id = null;
        $this->jurusan_items = null;
        $this->jurusanNameSearch = '';

        $this->updatedJurusanNameSearch('');
        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);
    }
}
