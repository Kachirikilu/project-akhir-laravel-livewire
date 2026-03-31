<?php

namespace App\Livewire\Global;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithMatkulSearchFilters
{
    use WithPagination;

    public $matkulSearchQuery = '';

    public $matkulSearchResults = [];

    public $matkul_name = '';

    public $matkul_name_array = [];

    public $matkul_id;

    public $matkul_id_array = [];

    public $matkul_kode;

    public $matkul_kode_array = [];

    public $matkulNameSearch = '';

    public $matkulResults = [];

    public $selectedMatkulId = null;

    public $mkType = '';

    public $showMKModal = false;

    public function inputMatkulFilter()
    {
        $search = trim($this->matkulSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->matkul_name) {
            $this->matkulSearchResults = MataKuliah::query()
                // ->with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])
                ->searchMK($search)
                ->limit(12)
                ->get()
                ->map(fn ($mk) => [
                    'id' => $mk->id,
                    'kode' => $mk->kode,
                    'matkul' => $mk->matkul,
                    'semester' => $mk->semester,
                    'sks' => $mk->sks,
                    'tipe_sks_text' => $mk->tipe_sks_text,
                    'wajib_text' => $mk->wajib_text,
                    'tingkatan_mk' => $mk->tingkatan_mk // 1, 2, 3, 4
                ])->toArray();

        } elseif (empty($search) || $this->matkul_name) {
            $this->matkulSearchResults = $this->getMatkulbyUser();
        } else {
            $this->matkulSearchResults = [];
        }
    }

    public function resetMatkulFilter()
    {
        $this->reset(['selectedMatkulId', 'matkul_name', 'matkulSearchQuery', 'matkul_kode']);
        $this->resetPage();
    }

    public function selectMatkulForFilter($id)
    {
        $data = MataKuliah::
        // with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])->
        find($id);

        if ($data) {
            $this->selectedMatkulId = $id;
            $this->matkul_kode = $data->kode;
            $this->matkul_name = $data->matkul;
            $this->matkulSearchQuery = $data->matkul;
            $this->matkulSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedMatkulNameSearch($value)
    {
        // 1. Reset State Awal
        $this->matkul_id = null;
        $this->matkul_kode = null;
        $this->resetErrorBag(['matkul_id', 'matkulNameSearch']);

        // 2. Inisialisasi Query Dasar (Gunakan select matkuls.* untuk menghindari ID tertimpa join)
        $query = MataKuliah::query()
            ->select('matkuls.*')
            // ->with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])
            ;

        // 3. PRIORITAS: Filter Berdasarkan Mode Mata Kuliah (Scope Constraints)
        // if ($this->showMKModal) {
        //     if (($this->mkType === 'mk-jurusan' || $this->mkType === 2) && filled($this->jurusan_id)) {
        //         $query->where('matkuls.jurusan_id', $this->jurusan_id);
        //     } elseif (($this->mkType === 'mk-fakultas' || $this->mkType === 3) && filled($this->fakultas_id)) {
        //         $query->whereHas('jurusan_rel', function ($q) {
        //             $q->where('fakultas_id', $this->fakultas_id);
        //         });
        //     }
        // }

        // 4. Logika Pencarian (Jika User Mengetik Sesuatu)
        if (trim(strlen($value)) > 0) {
            $results = $query
                ->searchMatkul($value)
                ->limit(12)->get();

            // Mapping Hasil Pencarian
            $this->matkulResults = $results->map(function ($matkul) {
                return [
                    'id' => $matkul->id,
                    'kode' => $matkul->kode,
                    'matkul' => $matkul->matkul,
                    'semester' => $matkul->semester,
                    'sks' => $matkul->sks,
                    'tipe_sks_text' => $matkul->tipe_sks_text,
                    'wajib_text' => $matkul->wajib_text,
                    'tingkatan_mk' => $matkul->tingkatan_mk // 1, 2, 3, 4
                ];
            })->toArray();

            $exactMatch = $results->first(function ($matkul) use ($value) {
                $input = str($value)->lower()->trim();

                $namaMatkul = str($matkul->matkul)->lower()->trim();
                $kodeMatkul = str($matkul->kode)->lower()->trim();
                // $namaStrata = str($matkul->strata)->lower()->trim();

                // $inisialStrata = match ($namaStrata->toString()) {
                //     'sarjana' => 's1',
                //     'magister' => 's2',
                //     'doktor' => 's3',
                //     default => ''
                // };

                $possibilities = [
                    $namaMatkul->toString(),
                    $kodeMatkul->toString(),
                    // "$inisialStrata $namaMatkul",
                    // "$namaStrata $namaMatkul",
                    // "$inisialStrata$namaMatkul",
                ];

                return in_array($input->toString(), $possibilities);
            });

            if ($exactMatch) {
                $this->matkul_id = $exactMatch->id;
                $this->matkul_kode = $exactMatch->kode;
                $this->matkulNameSearch = $exactMatch->matkul;
                $this->matkulResults = [];
            }
        }
        // 5. Default State (Jika input kosong)
        else {
            // if ($this->showMKModal && ($this->jurusan_id || $this->fakultas_id)) {
            //     $this->matkulResults = $query->orderBy('matkuls.nama_matkul')
            //         ->limit(12)
            //         ->get()
            //         ->map(fn ($mk) => [
            //             'id' => $mk->id,
            //             'kode' => $mk->kode,
            //             'matkul' => $mk->matkul,
            //             'semester' => $mk->semester,
            //             'sks' => $mk->sks,
            //             'tipe_sks_text' => $mk->tipe_sks_text,
            //             'wajib_text' => $mk->wajib_text
            //         ])->toArray();
            // } else {
                $this->matkulResults = $this->getMatkulbyUser();
            // }
        }
    }


    public function getMatkulbyUser()
    {
        $user = Auth::user()?->admin ?? Auth::user()?->dosen ?? Auth::user()?->mahasiswa;
        $userMatkul = $user ? $user->prodi()->first() : null;

        $user = Auth::user();
        $prodiIdUser = $user->prodi_id ?? null;
        $jurusanIdUser = $user->jurusan_id ?? null;
        $fakultasIdUser = $user->fakultas_id ?? null;
        

        // Jika tidak ada user/matkul, kembalikan 12 matkul pertama secara simpel
        if (! $prodiIdUser) {
            return MataKuliah::query()
                // ->with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])
                ->orderBy('nama_matkul', 'asc')
                ->limit(12)
                ->get()
                ->map(fn ($mk) => [
                    'id' => $mk->id,
                    'kode' => $mk->kode,
                    'matkul' => $mk->matkul,
                    'semester' => $mk->semester,
                    'sks' => $mk->sks,
                    'tipe_sks_text' => $mk->tipe_sks_text,
                    'wajib_text' => $mk->wajib_text,
                    'tingkatan_mk' => $mk->tingkatan_mk // 1, 2, 3, 4
                ])->toArray();
        }

        $query = MataKuliah::query()
        // ->with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])
        ;

        // if (($this->mkType == 2) && filled($this->jurusan_id) && $this->showMKModal) {
        //     $query->where('jurusan_id', $this->jurusan_id);
        // } elseif (($this->mkType == 3) && filled($this->fakultas_id) && $this->showMKModal) {
        //     $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->fakultas_id));
        // } else {
        //     $query->whereHas('jurusan_rel', fn ($q) => $q->where('fakultas_id', $fakultasIdUser));
        // }

        $mainResults = $query->get()->sortBy(function ($p) use ($prodiIdUser, $jurusanIdUser, $fakultasIdUser) {
            if ($p->id === $prodiIdUser) {
                return 0;
            }
            if ($p->jurusan_id === $jurusanIdUser) {
                return 1;
            }
            if ($p->fakultas_id === $fakultasIdUser) {
                return 2;
            }

            return 3;
        })->take(12);

        if ($mainResults->count() < 12 && empty($this->mkType)) {
            $extra = MataKuliah::query()->with(['prodis', 'jurusan_rel', 'jurusan_rel.fakultas_rel'])
                ->whereHas('prodis', fn ($q) => $q->where('prodi_id', '!=', $prodiIdUser))
                ->whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mainResults->map(fn ($mk) => [
            'id' => $mk->id,
            'kode' => $mk->kode,
            'matkul' => $mk->matkul,
            'semester' => $mk->semester,
            'sks' => $mk->sks,
            'tipe_sks_text' => $mk->tipe_sks_text,
            'wajib_text' => $mk->wajib_text,
            'tingkatan_mk' => $mk->tingkatan_mk // 1, 2, 3, 4
        ])->toArray();
    }

    public function fetchMatkul($query = '')
    {
        if (empty($query) || $this->matkul_id) {
            $this->matkulResults = $this->getMatkulbyUser();

            return;
        }
    }

    public function selectMatkul($id, $matkulName)
    {
        $this->matkul_id = $id;
        $this->matkulNameSearch = $matkulName;

        $data = MataKuliah::
        // with(['jurusan_rel', 'jurusan_rel.fakultas_rel'])->
        find($id);

        if ($data) {
            $this->matkul_kode = $data->kode;
        }

        $this->matkulResults = $this->getMatkulbyUser();
        $this->resetErrorBag(['matkul_id', 'matkulNameSearch']);
    }

    public function selectMatkulArray($id)
    {
        $data = MataKuliah::find($id);
        if ($data && ! in_array($id, $this->matkul_id_array)) {
            $this->matkul_id_array[] = $id;
            $this->matkul_name_array[] = $data->matkul;
            $this->matkul_kode_array[] = $data->kode;
        }
    }

    public function resetMatkulInput()
    {
        $this->matkul_id = null;
        $this->matkul_kode = null;
        $this->matkulNameSearch = '';

        $this->updatedMatkulNameSearch('');
        $this->resetErrorBag(['matkul_id', 'matkulNameSearch']);
    }

    public function resetMatkulArray()
    {
        $this->matkul_id_array = [];
        $this->matkul_name_array = [];
        $this->matkul_kode_array = [];
        $this->matkulNameSearch = '';
    }
}
