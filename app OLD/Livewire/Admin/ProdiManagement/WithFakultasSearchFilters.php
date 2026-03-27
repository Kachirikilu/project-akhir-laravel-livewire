<?php

namespace App\Livewire\Admin\ProdiManagement;

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

    public $fakultasNameSearch = '';

    public $fakultasResults = [];

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
        if (strlen($this->fakultasSearchQuery) > 1 || is_numeric($this->fakultasSearchQuery)) {
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
        } elseif (empty($this->fakultasSearchQuery)) {
            $this->fakultasSearchResults = $this->getFakultasbyUser();
        } else {
            $this->fakultasSearchResults = [];
        }
    }

    public function resetFakultasFilter()
    {
        $this->reset(['selectedFakultasId', 'fakultas_name', 'fakultasSearchQuery']);
        $this->resetPage();
    }

    public function selectFakultasForFilter($fakultasId)
    {
        $fakultas = Fakultas::find($fakultasId);
        if ($fakultas) {
            $this->selectedFakultasId = $fakultasId;
            $this->fakultas_name = 'Fakultas '.$fakultas->nama_fakultas;
            $this->fakultasSearchQuery = '';
            $this->resetPage();
        }
    }

    public function updatedFakultasNameSearch($value)
    {
        $this->fakultas_id = null;
        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);

        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = Fakultas::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm);
                })
                ->limit(12)
                ->get();

            $this->fakultasResults = $results->map(function ($fakultas) {
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
                $this->fakultasNameSearch = $exactMatch->nama_fakultas;
                $this->fakultasResults = [];
            }

        } else {
            if (Auth::user()->admin?->prodi_id) {
                $this->fakultasResults = $this->getFakultasbyUser();
            } else {
                $this->fakultasResults = Fakultas::query()
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

    public function selectFakultas($fakultasId, $fakultasName)
    {
        $this->fakultas_id = $fakultasId;
        $this->fakultasNameSearch = $fakultasName;
        $this->getFakultasbyUser();
        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);
    }

    public function resetFakultasInput()
    {
        $this->fakultas_id = null;
        $this->fakultasNameSearch = '';
        $this->updatedFakultasNameSearch('');
        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);
    }
}
