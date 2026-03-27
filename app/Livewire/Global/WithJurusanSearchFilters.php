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

    public $jurusan_name = '';

    public $jurusan_id;

    public $jurusan_kode;

    public $jurusanNameSearch = '';

    public $jurusanResults = [];

    public $selectedJurusanId = null;

    public function inputJurusanFilter()
    {
        $searchTerm = '%'.$this->jurusanSearchQuery.'%';

        if ((strlen($this->jurusanSearchQuery) > 1 || is_numeric($this->jurusanSearchQuery)) && ! $this->jurusan_name) {

            $this->jurusanSearchResults = Jurusan::query()
                ->with('fakultas_rel')
                ->searchJurusan($searchTerm)
                ->limit(12)
                ->get()
                ->map(fn ($j) => [
                    'id' => $j->id,
                    'kode' => $j->kode,
                    'jurusan' => $j->jurusan,
                    'fakultas' => $j->fakultas,
                ])
                ->toArray();

        } elseif (empty($this->jurusanSearchQuery) || $this->jurusan_name) {
            $this->jurusanSearchResults = $this->getJurusanbyUser();
        } else {
            $this->jurusanSearchResults = [];
        }
    }

    public function resetJurusanFilter()
    {
        $this->reset(['selectedJurusanId', 'jurusan_name', 'jurusanSearchQuery', 'jurusan_kode']);
        $this->resetPage();
    }

    public function selectJurusanForFilter($id)
    {
        $data = Jurusan::with('fakultas_rel')->find($id);

        if ($data) {
            $this->selectedJurusanId = $id;
            $this->jurusan_kode = $data->kode;
            $this->jurusan_name = 'Jurusan '.$data->jurusan;
            $this->jurusanSearchQuery = 'Jurusan '.$data->jurusan;
            $this->jurusanSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedJurusanNameSearch($value)
    {
        $this->jurusan_id = null;
        $this->jurusan_kode = null;
        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);

        if (strlen($value) > 0) {
            $searchTerm = '%'.$value.'%';

            $results = Jurusan::query()
                ->with('fakultas_rel')
                ->searchJurusan($searchTerm)
                ->limit(12)
                ->get();

            $this->jurusanResults = $results->map(function ($jurusan) {
                return [
                    'id' => $jurusan->id,
                    'kode' => $jurusan->kode,
                    'jurusan' => $jurusan->jurusan,
                    'fakultas' => $jurusan->fakultas,
                ];
            })->toArray();

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
                $this->jurusan_kode = $exactMatch->kode;
                $this->jurusanNameSearch = 'Jurusan '.$exactMatch->jurusan;
                $this->jurusanResults = [];
            }

        } else {
            if (Auth::user()->admin?->prodi_id) {
                $this->jurusanResults = $this->getJurusanbyUser();
            } else {
                $this->jurusanResults = Jurusan::with('fakultas_rel')
                    ->orderBy('nama_jurusan')
                    ->limit(12)
                    ->get()
                    ->map(fn ($j) => [
                        'id' => $j->id,
                        'kode' => $j->kode,
                        'jurusan' => $j->jurusan,
                        'fakultas' => $j->fakultas,
                    ])->toArray();
            }
        }
    }

    public function getJurusanbyUser()
    {
        $user = Auth::user()?->admin ?? Auth::user()?->dosen ?? Auth::user()?->mahasiswa;
        $userProdi = $user ? $user->prodi()->first() : null;

        $jurusanIdUser = $userProdi->jurusan_id ?? null;
        $fakultasIdUser = $userProdi->jurusan_rel?->fakultas_id ?? null;

        if (! $jurusanIdUser) {
            return Jurusan::with('fakultas_rel')
                ->orderBy('nama_jurusan', 'asc')
                ->limit(12)
                ->get()
                ->map(fn ($f) => [
                    'id' => $f->id,
                    'kode' => $f->kode,
                    'jurusan' => $f->jurusan,
                    'fakultas' => $f->fakultas,
                ])->toArray();
        }

        $results = Jurusan::with('fakultas_rel')
            ->where('fakultas_id', $fakultasIdUser)
            ->get()
            ->sortBy(fn ($j) => $j->id === $jurusanIdUser ? 0 : 1)
            ->take(12);

        if ($results->count() < 12) {
            $additional = Jurusan::with('fakultas_rel')
                ->where('fakultas_id', '!=', $fakultasIdUser)
                ->whereNotIn('id', $results->pluck('id'))
                ->orderBy('nama_jurusan', 'asc')
                ->limit(12 - $results->count())
                ->get();

            $results = $results->concat($additional);
        }

        return $results->map(fn ($f) => [
            'id' => $f->id,
            'kode' => $f->kode,
            'jurusan' => $f->jurusan,
            'fakultas' => $f->fakultas,
        ])->toArray();
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
        $this->jurusanNameSearch = 'Jurusan '.$jurusanName;
        $this->jurusanResults = $this->getJurusanbyUser();

        $data = Jurusan::with('fakultas_rel')->find($id);
        if ($data) {
            $this->jurusan_kode = $data->kode;
        }

        if (property_exists($this, 'prodi_id_array')) {
            $this->prodi_id_array = [];
            $this->prodi_name_array = [];
            $this->prodi_kode_array = [];
            $this->prodiNameSearch = '';
        }

        if (method_exists($this, 'fetchProdi')) {
            $this->fetchProdi('');
        }

        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);
    }

    public function resetJurusanInput()
    {
        $this->jurusan_id = null;
        $this->jurusan_kode = null;
        $this->jurusanNameSearch = '';

        if (property_exists($this, 'prodi_id_array')) {
            $this->prodi_id_array = [];
            $this->prodi_name_array = [];
            $this->prodi_kode_array = [];
        }

        $this->updatedJurusanNameSearch('');
        $this->resetErrorBag(['jurusan_id', 'jurusanNameSearch']);
    }
}
