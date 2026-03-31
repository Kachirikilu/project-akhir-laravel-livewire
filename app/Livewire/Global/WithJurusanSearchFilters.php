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

    private function mapJurusan($collection)
    {
        return $collection->map(fn ($jr) => [
            'id' => $jr->id,
            'kode' => $jr->kode,
            'jurusan' => $jr->jurusan,
            'fakultas' => $jr->fakultas
        ])->toArray();
    }

    public function inputJurusanFilter()
    {
        $search = trim($this->jurusanSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->jurusan_name) {
            $this->jurusanSearchResults = $this->mapJurusan(
                Jurusan::query()
                    ->with('fakultas_rel')
                    ->searchProdi($search)
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

        $query = Jurusan::query()
            ->select('jurusans.*')
            ->with('fakultas_rel');

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
                $this->jurusan_kode = $exactMatch->kode;
                $this->jurusanNameSearch = 'Jurusan '.$exactMatch->jurusan;
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

        $query = Jurusan::query()->with('fakultas_rel');

        if (! $jurusanId) {
            $defaultJurusans = Jurusan::query()
                    ->with('fakultas_rel')
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
            $extra = Jurusan::query()->with('fakultas_rel')
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

        if (method_exists($this, 'fetchJurusan')) {
            $this->fetchJurusan('');
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
