<?php

namespace App\Livewire\Admin\GlobalManagement;

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

    public $selected_kode_fk;

    public $fakultas_name_search = '';

    public $fakultas_results = [];

    public $selectedFakultasId = null;

    // public function inputFakultasFilter()
    // {
    //     $searchTerm = '%'.$this->fakultasSearchQuery.'%';

    //     if ((strlen($this->fakultasSearchQuery) > 1 || is_numeric($this->fakultasSearchQuery)) && ! $this->selectedFakultasName) {
    //         $this->fakultasSearchResults = Fakultas::query()
    //             ->where(function ($q) use ($searchTerm) {
    //                 $q->where('nama_fakultas', 'like', $searchTerm)
    //                     ->orWhere('id', 'like', $searchTerm)
    //                     ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
    //             })
    //             ->limit(12)
    //             ->get()
    //             ->map(fn ($f) => [
    //                 'id' => $f->id,
    //                 'kode' => $f->kode_fk ?? 'UNI',
    //                 'fakultas' => $f->nama_fakultas,
    //             ])->toArray();
    //     } elseif (empty($this->fakultasSearchQuery) || $this->selectedFakultasName) {
    //         $this->fakultasSearchResults = $this->getFakultasbyUser();
    //     } else {
    //         $this->fakultasSearchResults = [];
    //     }
    // }

    public function inputFakultasFilter()
    {
        $searchTerm = '%'.$this->fakultasSearchQuery.'%';

        if ((strlen($this->fakultasSearchQuery) > 1 || is_numeric($this->fakultasSearchQuery)) && ! $this->selectedFakultasName) {

            $this->fakultasSearchResults = Fakultas::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhere('kode_fk', 'like', $searchTerm) // 🔹 Cari berdasarkan kode fakultas (Contoh: 'FT', 'FK')
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                })
                ->limit(12)
                ->get()
                ->map(fn ($f) => [
                    'id' => $f->id,
                    'kode' => $f->kode_fk ?? 'UNI',
                    'fakultas' => $f->nama_fakultas,
                ])->toArray();

        } elseif (empty($this->fakultasSearchQuery) || $this->selectedFakultasName) {
            $this->fakultasSearchResults = $this->getFakultasbyUser();
        } else {
            $this->fakultasSearchResults = [];
        }
    }

    public function resetFakultasFilter()
    {
        $this->reset(['selectedFakultasId', 'selectedFakultasName', 'fakultasSearchQuery', 'selected_kode_fk']);
        $this->resetPage();
    }

    public function selectFakultasForFilter($id)
    {
        $data = Fakultas::find($id);
        if ($data) {
            $this->selectedFakultasId = $id;
            $this->selected_kode_fk = $data->kode_fk ?? 'UNI';
            $this->selectedFakultasName = 'Fakultas '.$data->nama_fakultas;
            $this->fakultasSearchQuery = 'Fakultas '.$data->nama_fakultas;
            $this->fakultasSearchResults = [];
            $this->resetPage();
        }
    }

    // public function updatedFakultasNameSearch($value)
    // {
    //     $this->fakultas_id = null;
    //     $this->selected_kode_fk = null;
    //     $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);

    //     if (strlen($value) > 0) {
    //         $searchTerm = '%'.$value.'%';

    //         $results = Fakultas::query()
    //             ->where(function ($q) use ($searchTerm) {
    //                 $q->where('nama_fakultas', 'like', $searchTerm)
    //                     ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm])
    //                     ->orWhere('id', 'like', $searchTerm);
    //             })
    //             ->limit(12)
    //             ->get();

    //         $this->fakultas_results = $results->map(function ($fakultas) {
    //             return [
    //                 'id' => $fakultas->id,
    //                 'kode' => $fakultas->kode_fk ?? 'UNI',
    //                 'fakultas' => $fakultas->nama_fakultas,
    //             ];
    //         })->toArray();

    //         $exactMatch = $results->first(function ($fakultas) use ($value) {
    //             $input = str($value)->lower()->trim();
    //             $kode = str($fakultas->kode_fk)->lower();
    //             $nama = str($fakultas->nama_fakultas)->lower();

    //             return $input->is([$nama, "fakultas $nama"]);
    //         });

    //         if ($exactMatch) {
    //             $this->fakultas_id = $exactMatch->id;
    //             $this->selected_kode_fk = $exactMatch->kode_fk ?? 'UNI';
    //             $this->fakultas_name_search = 'Fakultas '.$exactMatch->nama_fakultas;
    //             $this->fakultas_results = [];
    //         }

    //     } else {
    //         if (Auth::user()->admin?->prodi_id) {
    //             $this->fakultas_results = $this->getFakultasbyUser();
    //         } else {
    //             $this->fakultas_results = Fakultas::query()
    //                 ->orderBy('nama_fakultas')
    //                 ->limit(12)
    //                 ->get()
    //                 ->map(fn ($f) => [
    //                     'id' => $f->id,
    //                     'kode' => $f->kode_fk ?? 'UNI',
    //                     'fakultas' => $f->nama_fakultas,
    //                 ])->toArray();
    //         }
    //     }
    // }

    public function updatedFakultasNameSearch($value)
    {
        $this->fakultas_id = null;
        $this->selected_kode_fk = null;
        $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);

        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = Fakultas::query()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhere('kode_fk', 'like', $searchTerm) // 🔹 Tambahkan pencarian kode fakultas
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm])
                        ->orWhere('id', 'like', $searchTerm);
                })
                ->limit(12)
                ->get();

            $this->fakultas_results = $results->map(function ($fakultas) {
                return [
                    'id' => $fakultas->id,
                    'kode' => $fakultas->kode_fk ?? 'UNI',
                    'fakultas' => $fakultas->nama_fakultas,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($fakultas) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($fakultas->nama_fakultas)->lower();
                $kode = str($fakultas->kode_fk)->lower();

                // 🔹 Exact match sekarang mendukung nama, prefiks "fakultas", dan kode
                return $input->is([
                    $nama,
                    "fakultas $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->fakultas_id = $exactMatch->id;
                $this->selected_kode_fk = $exactMatch->kode_fk ?? 'UNI';
                $this->fakultas_name_search = 'Fakultas '.$exactMatch->nama_fakultas;
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
                        'kode' => $f->kode_fk ?? 'UNI',
                        'fakultas' => $f->nama_fakultas,
                    ])->toArray();
            }
        }
    }

    public function getFakultasbyUser()
    {
        $admin = Auth::user()?->admin;
        $userProdi = $admin ? $admin->prodi()->first() : null;
        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id ?? null;

        if (! $fakultasIdUser) {
            return Fakultas::query()
                ->orderBy('nama_fakultas', 'asc')
                ->limit(12)
                ->get()
                ->map(fn ($f) => [
                    'id' => $f->id,
                    'kode' => $f->kode_fk ?? 'UNI',
                    'fakultas' => $f->nama_fakultas,
                ])->toArray();
        }

        $results = Fakultas::query()
            ->where('id', $fakultasIdUser)
            ->get(['id', 'nama_fakultas', 'kode_fk']);

        $count = $results->count();

        if ($count < 12) {
            $additional = Fakultas::query()
                ->where('id', '!=', $fakultasIdUser)
                ->orderBy('nama_fakultas', 'asc')
                ->limit(12 - $count)
                ->get(['id', 'nama_fakultas', 'kode_fk']);

            $results = $results->concat($additional);
        }

        return $results->map(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_fk ?? 'UNI',
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

        $data = Fakultas::find($id);
        if ($data) {
            $this->selected_kode_fk = $data->kode_fk ?? 'UNI';
        }

        $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);
    }

    public function resetFakultasInput()
    {
        $this->fakultas_id = null;
        $this->selected_kode_fk = null;
        $this->fakultas_name_search = '';
        $this->updatedFakultasNameSearch('');
        $this->resetErrorBag(['fakultas_id', 'fakultas_name_search']);
    }
}
