<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithProdiSearchFilters
{
    use WithPagination;

    public $prodiSearchQuery = '';

    public $prodiSearchResults = [];

    public $prodi_name = '';

    public $prodi_id;

    public $prodiNameSearch = '';

    public $prodiResults = [];

    public $selectedProdiId = null;

    public function inputProdiFilter()
    {
        $searchTerm = '%'.$this->prodiSearchQuery.'%';

        if (strlen($this->prodiSearchQuery) > 1 || is_numeric($this->prodiSearchQuery)) {
            $this->prodiSearchResults = Prodi::with(['jurusan_rel.fakultas_rel'])
                ->where('nama_prodi', 'like', $searchTerm)
                ->orWhere('id', $this->prodiSearchQuery)
                ->orWhereHas('jurusan_rel', function ($q) use ($searchTerm) {
                    $q->where('nama_jurusan', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                        ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
                            $sq->where('nama_fakultas', 'like', $searchTerm)
                                ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                        });
                })
                ->limit(12)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'prodi' => $p->prodi,
                    'jurusan' => $p->jurusan,
                    'fakultas' => $p->fakultas,
                ])->toArray();
        } elseif (empty($this->prodiSearchQuery)) {
            $this->prodiSearchResults = $this->getProdibyUser();
        } else {
            $this->prodiSearchResults = [];
        }
    }

    public function resetProdiFilter()
    {
        $this->reset(['selectedProdiId', 'prodi_name', 'prodiSearchQuery']);
        $this->resetPage();
    }

    public function selectProdiForFilter($prodiId)
    {
        $prodi = Prodi::find($prodiId);
        if ($prodi) {
            $this->selectedProdiId = $prodiId;
            $this->prodi_name = $prodi->nama_prodi;
            $this->prodiSearchQuery = '';
            $this->resetPage();
        }
    }

    public function updatedProdiNameSearch($value)
    {
        $this->prodi_id = null;
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);

        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = Prodi::query()
                ->with(['jurusan_rel.fakultas_rel'])
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_prodi', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhereHas('jurusan_rel', function ($sq) use ($searchTerm) {
                            $sq->where('nama_jurusan', 'like', $searchTerm)
                                ->orWhereHas('fakultas_rel', function ($ssq) use ($searchTerm) {
                                    $ssq->where('nama_fakultas', 'like', $searchTerm);
                                });
                        });
                })
                ->limit(12)
                ->get();

            $this->prodiResults = $results->map(function ($prodi) {
                return [
                    'id' => $prodi->id,
                    'prodi' => $prodi->nama_prodi,
                    'jurusan' => $prodi->jurusan,
                    'fakultas' => $prodi->fakultas,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($prodi) use ($value) {
                return strtolower($prodi->nama_prodi) === strtolower($value);
            });

            if ($exactMatch) {
                $this->prodi_id = $exactMatch->id;
                $this->prodiNameSearch = $exactMatch->nama_prodi;
                $this->prodiResults = [];
            }

        } else {
            if (Auth::user()->admin?->prodi_id) {
                $this->prodiResults = $this->getProdibyUser();
            } else {
                $this->prodiResults = Prodi::with(['jurusan_rel.fakultas_rel'])
                    ->orderBy('nama_prodi')
                    ->limit(12)
                    ->get()
                    ->map(fn ($p) => [
                        'id' => $p->id,
                        'prodi' => $p->nama_prodi,
                        'jurusan' => $p->jurusan,
                        'fakultas' => $p->fakultas,
                    ])->toArray();
            }
        }
    }

    public function getProdibyUser()
    {
        $userProdi = Auth::user()?->admin?->prodi()->first();

        if (! $userProdi) {
            return [];
        }

        $namaProdiUser = $userProdi->nama_prodi;
        $jurusanIdUser = $userProdi->jurusan_id;
        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id;

        $mainResults = Prodi::query()
            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
            ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
            ->where('jurusans.fakultas_id', $fakultasIdUser)
            ->orderByRaw('
            CASE 
                WHEN prodis.nama_prodi = ? THEN 0 
                WHEN prodis.jurusan_id = ? THEN 1 
                WHEN jurusans.fakultas_id = ? THEN 2 
                ELSE 3 
            END ASC
        ', [$namaProdiUser, $jurusanIdUser, $fakultasIdUser])
            ->orderBy('prodis.nama_prodi', 'asc')
            ->limit(12)
            ->get([
                'prodis.id',
                'prodis.nama_prodi',
                'jurusans.nama_jurusan',
                'fakultas.nama_fakultas',
            ]);

        $countMain = $mainResults->count();

        if ($countMain < 12) {

            $remaining = 12 - $countMain;

            $extraResults = Prodi::query()
                ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->where('jurusans.fakultas_id', '!=', $fakultasIdUser)
                ->whereNotIn('prodis.id', $mainResults->pluck('id'))
                ->orderBy('prodis.nama_prodi', 'asc')
                ->limit($remaining)
                ->get([
                    'prodis.id',
                    'prodis.nama_prodi',
                    'jurusans.nama_jurusan',
                    'fakultas.nama_fakultas',
                ]);

            $mainResults = $mainResults->concat($extraResults);
        }

        return $mainResults->map(function ($item) {
            return [
                'id' => $item->id,
                'prodi' => $item->nama_prodi,
                'jurusan' => $item->nama_jurusan,
                'fakultas' => $item->nama_fakultas,
            ];
        })->toArray();
    }

    public function selectProdi($prodiId, $prodiName)
    {
        $this->prodi_id = $prodiId;
        $this->prodiNameSearch = $prodiName;
        $this->getProdibyUser();
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }

    public function resetProdiInput()
    {
        $this->prodi_id = null;
        $this->prodiNameSearch = '';
        $this->updatedProdiNameSearch('');
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }
}
