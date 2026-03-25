<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Jurusan;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithJurusanSearchFilters
{
    use WithPagination;

    public $jurusanSearchQuery = '';

    public $jurusanSearchResults = [];

    public $jurusan_name = '';

    public $jurusan_id;

    public $jurusanNameSearch = '';

    public $jurusanResults = [];

    public $selectedJurusanId = null;

    // public function inputJurusanFilter()
    // {
    //     $searchTerm = '%'.$this->jurusanSearchQuery.'%';

    //     if (strlen($this->jurusanSearchQuery) > 1) {
    //         $this->jurusanSearchResults = Jurusan::query()
    //             ->where('nama_jurusan', 'like', $searchTerm)
    //             ->orWhere('id', $this->jurusanSearchQuery)
    //             ->limit(12)
    //             ->get()
    //             ->map(fn ($p) => [
    //                 'id' => $p->id,
    //                 'jurusan' => $p->nama_jurusan,
    //             ])->toArray();
    //     } elseif (empty($this->fakultasSearchQuery)) {
    //         $this->fakultasSearchResults = $this->getFakultasbyUser();
    //     } else {
    //         $this->fakultasSearchResults = [];
    //     }
    // }
    // public function inputJurusanFilter()
    // {
    //     $searchTerm = '%'.$this->jurusanSearchQuery.'%';

    //     if (strlen($this->jurusanSearchQuery) > 1 || is_numeric($this->jurusanSearchQuery)) {
    //         $this->jurusanSearchResults = Jurusan::query()
    //             ->where(function ($q) use ($searchTerm) {
    //                 $q->where('nama_jurusan', 'like', $searchTerm)
    //                     ->orWhere('id', 'like', $searchTerm)
    //                     ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm]);
    //             })
    //             ->limit(12)
    //             ->get()
    //             ->map(fn ($p) => [
    //                 'id' => $p->id,
    //                 'jurusan' => $p->nama_jurusan,
    //                 'fakultas' => $p->fakultas_rel?->nama_fakultas,
    //             ])->toArray();
    //     } elseif (empty($this->jurusanSearchQuery)) {
    //         $this->jurusanSearchResults = $this->getJurusanbyUser();
    //     } else {
    //         $this->jurusanSearchResults = [];
    //     }
    // }

    public function inputJurusanFilter()
    {
        $searchTerm = '%'.$this->jurusanSearchQuery.'%';

        if (strlen($this->jurusanSearchQuery) > 1 || is_numeric($this->jurusanSearchQuery)) {

            $this->jurusanSearchResults = Jurusan::query()
                ->with('fakultas_rel')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                        // 🔹 mencari berdasarkan fakultas
                        ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
                            $sq->where('nama_fakultas', 'like', $searchTerm)
                                ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                        });

                })
                ->limit(12)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'jurusan' => $p->nama_jurusan,
                    'fakultas' => $p->fakultas_rel?->nama_fakultas,
                ])
                ->toArray();

        } elseif (empty($this->jurusanSearchQuery)) {
            $this->jurusanSearchResults = $this->getJurusanbyUser();
        } else {
            $this->jurusanSearchResults = [];
        }
    }

    public function resetJurusanFilter()
    {
        $this->reset(['selectedJurusanId', 'jurusan_name', 'jurusanSearchQuery']);
        $this->resetPage();
    }

    public function selectJurusanForFilter($jurusanId)
    {
        $jurusan = Jurusan::find($jurusanId);
        if ($jurusan) {
            $this->selectedJurusanId = $jurusanId;
            $this->jurusan_name = $jurusan->nama_jurusan;
            $this->jurusanSearchQuery = '';
            $this->resetPage();
        }
    }

    public function updatedJurusanNameSearch($value)
    {
        $this->jurusan_id = null;
        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);

        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = Jurusan::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm);
                })
                ->limit(12)
                ->get();

            $this->jurusanResults = $results->map(function ($jurusan) {
                return [
                    'id' => $jurusan->id,
                    'jurusan' => $jurusan->nama_jurusan,
                    'fakultas' => $jurusan->fakultas_rel?->nama_fakultas,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($jurusan) use ($value) {
                return strtolower($jurusan->nama_jurusan) === strtolower($value);
            });

            if ($exactMatch) {
                $this->jurusan_id = $exactMatch->id;
                $this->jurusanNameSearch = $exactMatch->nama_jurusan;
                $this->jurusanResults = [];
            }

        } else {
            if (Auth::user()->admin?->prodi_id) {
                $this->jurusanResults = $this->getJurusanbyUser();
            } else {
                $this->jurusanResults = Jurusan::query()
                    ->orderBy('nama_jurusan')
                    ->limit(12)
                    ->get()
                    ->map(fn ($f) => [
                        'id' => $f->id,
                        'jurusan' => $f->nama_jurusan,
                        'fakultas' => $f->fakultas_rel?->nama_fakultas,
                    ])->toArray();
            }
        }
    }

    public function getJurusanbyUser()
    {
        $userProdi = Auth::user()?->admin?->prodi()->first();
        $jurusanIdUser = $userProdi->jurusan_id ?? null;
        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id ?? null;

        if (! $jurusanIdUser) {
            return [];
        }

        $results = Jurusan::query()
            ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
            ->where('jurusans.id', $jurusanIdUser)
            ->get([
                'jurusans.id',
                'jurusans.nama_jurusan',
                'fakultas.nama_fakultas',
            ]);

        $count = $results->count();

        if ($count < 12) {
            $additional = Jurusan::query()
                ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->where('jurusans.id', '!=', $jurusanIdUser)
                ->orderByRaw('CASE WHEN jurusans.fakultas_id = ? THEN 0 ELSE 1 END ASC', [$fakultasIdUser])
                ->orderBy('jurusans.nama_jurusan', 'asc')
                ->limit(12 - $count)
                ->get([
                    'jurusans.id',
                    'jurusans.nama_jurusan',
                    'fakultas.nama_fakultas',
                ]);

            $results = $results->concat($additional);
        }

        return $results->map(function ($item) {
            return [
                'id' => $item->id,
                'jurusan' => $item->nama_jurusan,
                'fakultas' => $item->nama_fakultas,
            ];
        })->toArray();
    }

    public function selectJurusan($jurusanId, $jurusanName)
    {
        $this->jurusan_id = $jurusanId;
        $this->jurusanNameSearch = $jurusanName;
        $this->getJurusanbyUser();
        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);
    }

    public function resetJurusanInput()
    {
        $this->jurusan_id = null;
        $this->jurusanNameSearch = '';
        $this->updatedJurusanNameSearch('');
        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);
    }
}
