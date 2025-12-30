<?php

namespace App\Livewire\Admin\UserManagement;

use Livewire\WithPagination;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;

trait WithProdiFilters
{
    use WithPagination;

    public $prodiSearchQuery = '';
    public $prodiSearchResults = [];
    public $selectedProdiName = '';

    public $prodi_id;
    public $prodi_name_search = '';
    public $prodi_results = [];

    public $selectedProdiId = null;


    public function inputProdiFilter() {
    $searchTerm = '%' . $this->prodiSearchQuery . '%';

    if (strlen($this->prodiSearchQuery) > 1) {
        $this->prodiSearchResults = Prodi::with(['jurusan_rel.fakultas'])
            ->where('nama_prodi', 'like', $searchTerm)
            ->orWhere('id', $this->prodiSearchQuery) 
            ->orWhereHas('jurusan_rel', function($q) use ($searchTerm) {
                $q->where('nama_jurusan', 'like', $searchTerm)
                ->orWhereHas('fakultas', function($sq) use ($searchTerm) {
                    $sq->where('nama_fakultas', 'like', $searchTerm);
                });
            })
            ->limit(10)
            ->get()
            ->map(fn($p) => [
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
        $this->reset(['selectedProdiId', 'selectedProdiName', 'prodiSearchQuery']);
        $this->resetPage();
    }

    public function selectProdiForFilter($prodiId)
    {
        $prodi = Prodi::find($prodiId);
        if ($prodi) {
            $this->selectedProdiId = $prodiId;
            $this->selectedProdiName = $prodi->nama_prodi;
            $this->prodiSearchQuery = '';
            $this->resetPage();
        }
    }

    public function updatedProdiNameSearch($value)
    {
        $this->prodi_id = null;
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);

        if (strlen($value) > 0) {
            $searchTerm = '%' . $value . '%';

            $results = Prodi::query()
                ->with(['jurusan_rel.fakultas'])
                ->where(function ($q) use ($searchTerm, $value) {
                    $q->where('nama_prodi', 'like', $searchTerm)
                    ->orWhere('id', 'like', $searchTerm)
                    ->orWhereHas('jurusan_rel', function ($sq) use ($searchTerm) {
                        $sq->where('nama_jurusan', 'like', $searchTerm)
                            ->orWhereHas('fakultas', function ($ssq) use ($searchTerm) {
                                $ssq->where('nama_fakultas', 'like', $searchTerm);
                            });
                    });
                })
                ->limit(5)
                ->get();

            $this->prodi_results = $results->map(function ($prodi) {
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
                $this->prodi_name_search = $exactMatch->nama_prodi;
                $this->prodi_results = []; 
            }

        } else {
            if (Auth::user()->admin?->prodi_id) {
                 $this->prodi_results = $this->getProdibyUser();
            } else {
                $this->prodi_results = Prodi::with(['jurusan_rel.fakultas'])
                    ->orderBy('nama_prodi')
                    ->limit(5)
                    ->get()
                    ->map(fn($p) => [
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
        $userProdi = Auth::user()?->admin?->prodi()->with('jurusan_rel.fakultas')->first();

        if (!$userProdi) {
            return [];
        }

        $namaProdiUser = $userProdi->nama_prodi;
        $jurusanIdUser = $userProdi->jurusan_id;
        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id;

        $results = Prodi::query()
            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
            ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
            ->where('jurusans.fakultas_id', $fakultasIdUser) 
            ->orderByRaw("
                CASE 
                    WHEN prodis.nama_prodi = ? THEN 0 
                    WHEN prodis.jurusan_id = ? THEN 1 
                    WHEN jurusans.fakultas_id = ? THEN 2 
                    ELSE 3 
                END ASC
            ", [$namaProdiUser, $jurusanIdUser, $fakultasIdUser])
            ->orderBy('prodis.nama_prodi', 'asc')
            ->limit(10)
            ->get([
                'prodis.id', 
                'prodis.nama_prodi', 
                'jurusans.nama_jurusan', 
                'fakultas.nama_fakultas'
            ]);

        return $results->map(function ($item) {
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
        $this->prodi_name_search = $prodiName;
        $this->getProdibyUser();
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']); 
    }
    public function resetProdiInput()
    {
        $this->prodi_id = null;
        $this->prodi_name_search = '';
        $this->updatedProdiNameSearch(''); 
        $this->resetErrorBag(['prodi_id', 'prodi_name_search']);
    }
}