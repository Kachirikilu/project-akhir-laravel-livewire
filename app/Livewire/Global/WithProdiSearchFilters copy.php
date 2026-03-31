<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithProdiSearchFilters
{
    use WithPagination;

    public $prodiSearchQuery = '';

    public $prodiSearchResults = [];

    public $prodi_name = '';

    public $prodi_name_array = [];

    public $prodi_id;

    public $prodi_id_array = [];

    public $prodi_kode;

    public $prodi_kode_array = [];

    public $prodiNameSearch = '';

    public $prodiResults = [];

    public $selectedProdiId = null;

    public $mkType = '';

    public $showMKModal = false;


    private function mapProdi($collection)
    {
        return $collection->map(fn ($pr) => [
            'id' => $pr->id,
            'kode' => $pr->kode,
            'prodi' => $pr->prodi,
            'jurusan' => $pr->jurusan,
            'fakultas' => $pr->fakultas,
            'strata' => $pr->strata
        ])->toArray();
    }

    public function inputProdiFilter()
    {
        $search = trim($this->prodiSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->prodi_name) {
            $this->prodiSearchResults = $this->mapProdi(
                Prodi::query()
                    ->with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])
                    ->searchProdi($search)
                    ->limit(12)->get()
            );
        } elseif (empty($search) || $this->prodi_name) {
            $this->prodiSearchResults = $this->getProdibyUser();
        } else {
            $this->prodiSearchResults = [];
        }
    }


    public function resetProdiFilter()
    {
        $this->reset(['selectedProdiId', 'prodi_name', 'prodiSearchQuery', 'prodi_kode']);
        $this->resetPage();
    }

    public function selectProdiForFilter($id)
    {
        $data = Prodi::with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])->find($id);

        if ($data) {
            $this->selectedProdiId = $id;
            $this->prodi_kode = $data->kode;
            $this->prodi_name = $data->prodi;
            $this->prodiSearchQuery = $data->prodi;
            $this->prodiSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedProdiNameSearch($value)
    {
        // 1. Reset State Awal
        $this->prodi_id = null;
        $this->prodi_kode = null;
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);

        // 2. Inisialisasi Query Dasar (Gunakan select prodis.* untuk menghindari ID tertimpa join)
        $query = Prodi::query()
            ->select('prodis.*')
            ->with(['jurusan_rel', 'jurusan_rel.fakultas_rel']);

        // 3. PRIORITAS: Filter Berdasarkan Mode Mata Kuliah (Scope Constraints)
        if ($this->showMKModal) {
            if (($this->mkType === 'mk-jurusan' || $this->mkType === 2) && filled($this->jurusan_id)) {
                $query->where('prodis.jurusan_id', $this->jurusan_id);
            } elseif (($this->mkType === 'mk-fakultas' || $this->mkType === 3) && filled($this->fakultas_id)) {
                $query->whereHas('jurusan_rel', function ($q) {
                    $q->where('fakultas_id', $this->fakultas_id);
                });
            }
        }

        // 4. Logika Pencarian (Jika User Mengetik Sesuatu)
        if (trim(strlen($value)) > 0) {
            $results = $query->searchProdi($value)->limit(12)->get();
            $this->prodiResults = $this->mapProdi($results);

            $exactMatch = $results->first(function ($prodi) use ($value) {
                $input = str($value)->lower()->trim();

                $namaProdi = str($prodi->prodi)->lower()->trim();
                $kodeProdi = str($prodi->kode)->lower()->trim();
                $namaStrata = str($prodi->strata)->lower()->trim();

                $inisialStrata = match ($namaStrata->toString()) {
                    'sarjana' => 's1',
                    'magister' => 's2',
                    'doktor' => 's3',
                    default => ''
                };

                $possibilities = [
                    $namaProdi->toString(),
                    $kodeProdi->toString(),
                    "$inisialStrata $namaProdi",
                    "$namaStrata $namaProdi",
                    "$inisialStrata$namaProdi",
                ];

                return in_array($input->toString(), $possibilities);
            });

            if ($exactMatch) {
                $this->prodi_id = $exactMatch->id;
                $this->prodi_kode = $exactMatch->kode;
                $this->prodiNameSearch = $exactMatch->prodi;
                $this->prodiResults = [];
            }
        }
        // 5. Default State (Jika input kosong)
        else {
            if ($this->showMKModal && ($this->jurusan_id || $this->fakultas_id)) {
                $this->prodiResults = $this->mapProdi(
                    $query->orderBy('prodis.nama_prodi')->limit(12)->get()
                );
            } else {
                $this->prodiResults = $this->getProdibyUser();
            }
        }
    }

    public function getProdibyUser()
    {
        $user = Auth::user();
        $profile = $user->admin ?? $user->dosen ?? $user->mahasiswa;
        $prodiId = $profile?->prodi_id;
        $jurusanId = $profile->jurusan_id ?? null;
        $fakultasId = $profile->fakultas_id ?? null;

        // Jika tidak ada user/prodi, kembalikan 12 prodi pertama secara simpel
        if (! $prodiId) {
            $defaultProdis = Prodi::query()
                    ->with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])
                    ->orderBy('nama_prodi', 'asc')
                    ->limit(12)
                    ->get();

            return $this->mapProdi($defaultProdis);
        }

        $query = Prodi::query()->with(['jurusan_rel', 'jurusan_rel.fakultas_rel']);

        if (($this->mkType == 2) && filled($this->jurusan_id) && $this->showMKModal) {
            $query->where('jurusan_id', $this->jurusan_id);
        } elseif (($this->mkType == 3) && filled($this->fakultas_id) && $this->showMKModal) {
            $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->fakultas_id));
        } else {
            $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $fakultasId));
        }

        $mainResults = $query->get()->sortBy(function ($p) use ($prodiId, $jurusanId, $fakultasId) {
            if ($p->id === $prodiId) {
                return 0;
            }
            if ($p->jurusan_id === $jurusanId) {
                return 1;
            }
            if ($p->fakultas_id === $fakultasId) {
                return 2;
            }

            return 3;
        })->take(12);

        if ($mainResults->count() < 12 && empty($this->mkType)) {
            $extra = Prodi::query()->with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])
                ->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', '!=', $fakultasId))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapProdi($mainResults);
    }

    public function fetchProdi($query = '')
    {
        if (empty($query) || $this->prodi_id) {
            $this->prodiResults = $this->getProdibyUser();

            return;
        }
    }

    public function selectProdi($id, $prodiName)
    {
        $this->prodi_id = $id;
        $this->prodiNameSearch = $prodiName;

        $data = Prodi::with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])->find($id);
        if ($data) {
            $this->prodi_kode = $data->kode;
        }

        $this->prodiResults = $this->getProdibyUser();
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }

    public function selectProdiArray($id)
    {
        $data = Prodi::find($id);
        if ($data && ! in_array($id, $this->prodi_id_array)) {
            $this->prodi_id_array[] = $id;
            $this->prodi_name_array[] = $data->prodi;
            $this->prodi_kode_array[] = $data->kode;
        }
    }

    public function resetProdiInput()
    {
        $this->prodi_id = null;
        $this->prodi_kode = null;
        $this->prodiNameSearch = '';

        $this->updatedProdiNameSearch('');
        $this->resetErrorBag(['prodi_id', 'prodiNameSearch']);
    }

    public function resetProdiArray()
    {
        $this->prodi_id_array = [];
        $this->prodi_name_array = [];
        $this->prodi_kode_array = [];
        $this->prodiNameSearch = '';
    }
}
