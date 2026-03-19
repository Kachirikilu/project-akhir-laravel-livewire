<?php

namespace App\Livewire\Admin\GlobalManagement;

use App\Models\Jurusan;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithJurusanSearchFilters
{
    use WithPagination;

    public $jurusanSearchQuery = '';

    public $jurusanSearchResults = [];

    public $selectedJurusanName = '';

    public $jurusan_id;

    public $jurusan_name_search = '';

    public $jurusan_results = [];

    public $selectedJurusanId = null;

    public function inputJurusanFilter()
    {
        $searchTerm = '%'.$this->jurusanSearchQuery.'%';

        if ((strlen($this->jurusanSearchQuery) > 1 || is_numeric($this->jurusanSearchQuery)) && !$this->selectedJurusanName) {

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

        } elseif (empty($this->jurusanSearchQuery) || $this->selectedJurusanName) {
            $this->jurusanSearchResults = $this->getJurusanbyUser();
        } else {
            $this->jurusanSearchResults = [];
        }
    }

    public function resetJurusanFilter()
    {
        $this->reset(['selectedJurusanId', 'selectedJurusanName', 'jurusanSearchQuery']);
        $this->resetPage();
    }

    public function selectJurusanForFilter($id)
    {
        $data = Jurusan::find($id);
        if ($data) {
            $this->selectedJurusanId = $id;
            $this->selectedJurusanName = 'Jurusan '.$data->nama_jurusan;
            $this->jurusanSearchQuery = 'Jurusan '.$data->nama_jurusan;
            $this->jurusanSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedJurusanNameSearch($value)
    {
        $this->jurusan_id = null;
        $this->resetErrorBag(['jurusan_id', 'jurusan_name_search']);

        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = Jurusan::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                        ->orWhere('id', 'like', $searchTerm);
                })
                ->limit(12)
                ->get();

            $this->jurusan_results = $results->map(function ($jurusan) {
                return [
                    'id' => $jurusan->id,
                    'jurusan' => $jurusan->nama_jurusan,
                    'fakultas' => $jurusan->fakultas_rel?->nama_fakultas,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($jurusan) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($jurusan->nama_jurusan)->lower();
                return $input->is([$nama, "jurusan $nama"]);
            });

            if ($exactMatch) {
                $this->jurusan_id = $exactMatch->id;
                $this->jurusan_name_search = 'Jurusan '.$exactMatch->nama_jurusan;
                $this->jurusan_results = [];
            }

        } else {
            if (Auth::user()->admin?->prodi_id) {
                $this->jurusan_results = $this->getJurusanbyUser();
            } else {
                $this->jurusan_results = Jurusan::query()
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
        $admin = Auth::user()?->admin;
        $userProdi = $admin ? $admin->prodi()->first() : null;
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

    public function fetchJurusan($query = '')
    {
        if (empty($query) || $this->jurusan_id) {
            $this->jurusan_results = $this->getJurusanbyUser();

            return;
        }
    }

    public function selectJurusan($id, $jurusanName)
    {
        $this->jurusan_id = $id;
        $this->jurusan_name_search = 'Jurusan '.$jurusanName;
        $this->jurusan_results = $this->getJurusanbyUser();
        $this->resetErrorBag(['jurusan_id', 'jurusan_name_search']);
    }

    public function resetJurusanInput()
    {
        $this->jurusan_id = null;
        $this->jurusan_name_search = '';
        $this->updatedJurusanNameSearch('');
        $this->resetErrorBag(['jurusan_id', 'jurusan_name_search']);
    }
}
