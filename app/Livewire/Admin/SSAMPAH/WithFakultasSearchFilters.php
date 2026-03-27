<?php

namespace App\Livewire\Global;

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

    public $fakultas_kode;

    public $fakultasNameSearch = '';

    public $fakultasResults = [];

    public $selectedFakultasId = null;


    public function inputFakultasFilter()
    {
        $searchTerm = '%'.$this->fakultasSearchQuery.'%';

        if ((strlen($this->fakultasSearchQuery) > 1 || is_numeric($this->fakultasSearchQuery)) && ! $this->fakultas_name) {

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
                    'kode' => $f->kode ?? 'UNI',
                    'fakultas' => $f->fakultas,
                ])->toArray();

        } elseif (empty($this->fakultasSearchQuery) || $this->fakultas_name) {
            $this->fakultasSearchResults = $this->getFakultasbyUser();
        } else {
            $this->fakultasSearchResults = [];
        }
    }

    public function resetFakultasFilter()
    {
        $this->reset(['selectedFakultasId', 'fakultas_name', 'fakultasSearchQuery', 'fakultas_kode']);
        $this->resetPage();
    }

    public function selectFakultasForFilter($id)
    {
        $data = Fakultas::find($id);
        if ($data) {
            $this->selectedFakultasId = $id;
            $this->fakultas_kode = $data->kode ?? 'UNI';
            $this->fakultas_name = 'Fakultas '.$data->fakultas;
            $this->fakultasSearchQuery = 'Fakultas '.$data->fakultas;
            $this->fakultasSearchResults = [];
            $this->resetPage();
        }
    }


    public function updatedFakultasNameSearch($value)
    {
        $this->fakultas_id = null;
        $this->fakultas_kode = null;
        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);

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

            $this->fakultasResults = $results->map(function ($fakultas) {
                return [
                    'id' => $fakultas->id,
                    'kode' => $fakultas->kode ?? 'UNI',
                    'fakultas' => $fakultas->fakultas,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($fakultas) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($fakultas->fakultas)->lower();
                $kode = str($fakultas->kode)->lower();

                // 🔹 Exact match sekarang mendukung nama, prefiks "fakultas", dan kode
                return $input->is([
                    $nama,
                    "fakultas $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->fakultas_id = $exactMatch->id;
                $this->fakultas_kode = $exactMatch->kode ?? 'UNI';
                $this->fakultasNameSearch = 'Fakultas '.$exactMatch->fakultas;
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
                        'kode' => $f->kode ?? 'UNI',
                        'fakultas' => $f->fakultas,
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
                    'kode' => $f->kode ?? 'UNI',
                    'fakultas' => $f->fakultas,
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
                ->get(['id', 'kode_fk', 'nama_fakultas']);

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
            $this->fakultasResults = $this->getFakultasbyUser();

            return;
        }
    }

    public function selectFakultas($id, $fakultasName)
    {
        $this->fakultas_id = $id;
        $this->fakultasNameSearch = 'Fakultas '.$fakultasName;
        $this->fakultasResults = $this->getFakultasbyUser();

        $data = Fakultas::find($id);
        if ($data) {
            $this->fakultas_kode = $data->kode_fk ?? 'UNI';
        }

        if (property_exists($this, 'prodi_id_array')) {
            $this->prodi_id_array = [];
            $this->prodi_name_array = [];
            $this->prodi_kode_array = [];
            $this->prodiNameSearch = '';
        }


        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);
    }

    public function resetFakultasInput()
    {
        $this->fakultas_id = null;
        $this->fakultas_kode = null;
        $this->fakultasNameSearch = '';

        if (property_exists($this, 'prodi_id_array')) {
            $this->prodi_id_array = [];
            $this->prodi_name_array = [];
            $this->prodi_kode_array = [];
        }

        $this->updatedFakultasNameSearch('');
        $this->resetErrorBag(['fakultas_id', 'fakultasNameSearch']);
    }
}
