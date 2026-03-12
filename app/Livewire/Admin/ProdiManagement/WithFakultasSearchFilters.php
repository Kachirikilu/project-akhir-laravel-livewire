<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Fakultas;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithFakultasSearchFilters
{
    use WithPagination;

    public $fakultasSearchQuery = '';

    public $fakultasSearchResults = [];

    public $selectedFakultasName = '';

    public $fakultas_id;

    public $fakultas_name_search = '';

    public $fakultas_results = [];

    public $selectedFakultasId = null;

    // public function inputFakultasFilter()
    // {
    //     $searchTerm = '%'.$this->fakultasSearchQuery.'%';

    //     if (strlen($this->fakultasSearchQuery) > 1) {
    //         $this->fakultasSearchResults = Fakultas::query()
    //             ->where('nama_fakultas', 'like', $searchTerm)
    //             ->orWhere('id', $this->fakultasSearchQuery)
    //             ->limit(12)
    //             ->get()
    //             ->map(fn ($p) => [
    //                 'id' => $p->id,
    //                 'fakultas' => $p->nama_fakultas,
    //             ])->toArray();
    //     } elseif (empty($this->fakultasSearchQuery)) {
    //         $this->fakultasSearchResults = $this->getFakultasbyUser();
    //     } else {
    //         $this->fakultasSearchResults = [];
    //     }
    // }
    public function inputFakultasFilter()
    {
        $searchTerm = '%'.$this->fakultasSearchQuery.'%';

        if ((strlen($this->fakultasSearchQuery) > 1 || is_numeric($this->fakultasSearchQuery))  && !$this->selectedFakultasName) {
            $this->fakultasSearchResults = Fakultas::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                })
                ->limit(12)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'fakultas' => $p->nama_fakultas,
                ])->toArray();
        } elseif (empty($this->fakultasSearchQuery) || $this->selectedFakultasName) {
            $this->fakultasSearchResults = $this->getFakultasbyUser();
        } else {
            $this->fakultasSearchResults = [];
        }
    }

    public function resetFakultasFilter()
    {
        $this->reset(['selectedFakultasId', 'selectedFakultasName', 'fakultasSearchQuery']);
        $this->resetPage();
    }

    // public function selectFakultasForFilter($fakultasId)
    // {
    //     $fakultas = Fakultas::find($fakultasId);
    //     if ($fakultas) {
    //         $this->selectedFakultasId = $fakultasId;
    //         $this->selectedFakultasName = 'Fakultas '.$fakultas->nama_fakultas;
    //         $this->fakultasSearchQuery = '';
    //         $this->resetPage();
    //     }
    // }
    public function selectFakultasForFilter($id)
    {
        $data = Fakultas::find($id);
        if ($data) {
            $this->selectedFakultasId = $id;
            $this->selectedFakultasName = 'Fakultas '.$data->nama_fakultas;
            $this->fakultasSearchQuery = 'Fakultas '.$data->nama_fakultas;
            $this->fakultasSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedFakultasNameSearch($value)
    {
        $this->fakultas_id = null;
        $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);

        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = Fakultas::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm])
                        ->orWhere('id', 'like', $searchTerm);
                })
                ->limit(12)
                ->get();

            $this->fakultas_results = $results->map(function ($fakultas) {
                return [
                    'id' => $fakultas->id,
                    'fakultas' => $fakultas->nama_fakultas,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($fakultas) use ($value) {
                return strtolower($fakultas->nama_fakultas) === strtolower($value);
            });

            if ($exactMatch) {
                $this->fakultas_id = $exactMatch->id;
                $this->fakultas_name_search = 'Fakultas ' . $exactMatch->nama_fakultas;
                $this->fakultas_results = [];
            }

        } else {
            if (Auth::user()->admin?->prodi_id) {
                $this->fakultas_results = $this->getFakultasbyUser();
            } else {
                $this->fakultas_results = Fakultas::query()
                    ->orderBy('nama_fakultas')
                    ->limit(12)
                    ->get()
                    ->map(fn ($f) => [
                        'id' => $f->id,
                        'fakultas' => $f->nama_fakultas,
                    ])->toArray();
            }
        }
    }

    public function getFakultasbyUser()
    {
        $userProdi = Auth::user()?->admin?->prodi()->first();

        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_rel?->id ?? null;

        if (! $fakultasIdUser) {
            return [];
        }

        $results = Fakultas::query()
            ->where('id', $fakultasIdUser)
            ->orderBy('nama_fakultas', 'asc')
            ->limit(12)
            ->get(['id', 'nama_fakultas']);

        $count = $results->count();

        if ($count < 12) {
            $additional = Fakultas::query()
                ->where('id', '!=', $fakultasIdUser)
                ->orderBy('nama_fakultas', 'asc')
                ->limit(12 - $count)
                ->get(['id', 'nama_fakultas']);

            $results = $results->concat($additional);
        }

        return $results->map(function ($item) {
            return [
                'id' => $item->id,
                'fakultas' => $item->nama_fakultas,
            ];
        })->toArray();
    }

    public function fetchFakultas($query = '')
    {
        if (empty($query) || $this->fakultas_id) {
            $this->fakultas_results = $this->getFakultasbyUser();

            return;
        }
    }

    public function selectFakultas($id, $fakultasName)
    {
        $this->fakultas_id = $id;
        $this->fakultas_name_search = 'Fakultas '.$fakultasName;
        $this->fakultas_results = $this->getFakultasbyUser();
        $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);
    }

    // public function selectFakultas($fakultasId, $fakultasName)
    // {
    //     $this->fakultas_id = $fakultasId;
    //     $this->fakultas_name_search = 'Fakultas ' . $fakultasName;
    //     $this->getFakultasbyUser();
    //     $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);
    // }

    public function resetFakultasInput()
    {
        $this->fakultas_id = null;
        $this->fakultas_name_search = '';
        $this->updatedFakultasNameSearch('');
        $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);
    }
}
