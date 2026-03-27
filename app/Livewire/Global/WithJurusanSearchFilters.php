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
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhere('kode_jr', 'like', $searchTerm) // 🔹 Cari berdasarkan kode jurusan
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                        // 🔹 Mencari berdasarkan fakultas (Nama atau Kode)
                        ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
                            $sq->where('nama_fakultas', 'like', $searchTerm)
                                ->orWhere('kode_fk', 'like', $searchTerm) // 🔹 Cari berdasarkan kode fakultas
                                ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                        });
                })
                ->limit(12)
                ->get()
                ->map(fn ($j) => [
                    'id' => $j->id,
                    // Mengambil kode jurusan, jika null ambil kode fakultas
                    'kode' => $j->kode ?? 'UNI',
                    'jurusan' => $j->jurusan,
                    'fakultas' => $j->fakultas_rel?->fakultas,
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
            $this->jurusan_kode = $data->kode ?? 'UNI';
            $this->jurusan_name = 'Jurusan '.$data->urusan;
            $this->jurusanSearchQuery = 'Jurusan '.$data->urusan;
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
                ->where(function ($q) use ($searchTerm) {
                    $q->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhere('kode_jr', 'like', $searchTerm) // 🔹 Cari berdasarkan kode jurusan
                        ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                        ->orWhere('id', 'like', $searchTerm)
                        // 🔹 Mencari berdasarkan fakultas (Nama atau Kode)
                        ->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
                            $sq->where('nama_fakultas', 'like', $searchTerm)
                                ->orWhere('kode_fk', 'like', $searchTerm); // 🔹 Cari berdasarkan kode fakultas
                        });
                })
                ->limit(12)
                ->get();

            $this->jurusanResults = $results->map(function ($jurusan) {
                return [
                    'id' => $jurusan->id,
                    'kode' => $jurusan->kode ?? 'UNI',
                    'jurusan' => $jurusan->jurusan,
                    'fakultas' => $jurusan->fakultas_rel?->fakultas,
                ];
            })->toArray();

            $exactMatch = $results->first(function ($jurusan) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($jurusan->jurusan)->lower();
                $kode = str($jurusan->kode_jr)->lower();

                return $input->is([
                    $nama,
                    "jurusan $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->jurusan_id = $exactMatch->id;
                $this->jurusan_kode = $exactMatch->kode ?? 'UNI';
                $this->jurusanNameSearch = 'Jurusan '.$exactMatch->nama_jurusan;
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
                        'kode' => $j->kode ?? 'UNI',
                        'jurusan' => $j->jurusan,
                        'fakultas' => $j->fakultas_rel?->fakultas,
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
            return [];
        }

        $results = Jurusan::query()
            ->join('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
            ->where('jurusans.id', $jurusanIdUser)
            ->get([
                'jurusans.id',
                'jurusans.kode_jr',
                'jurusans.nama_jurusan',
                'fakultas.nama_fakultas',
                'fakultas.kode_fk',
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
                    'jurusans.kode_jr',
                    'jurusans.nama_jurusan',
                    'fakultas.nama_fakultas',
                    'fakultas.kode_fk',
                ]);

            $results = $results->concat($additional);
        }

        return $results->map(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode_jr ?? $item->kode_fk ?? 'UNI',
                'jurusan' => $item->nama_jurusan,
                'fakultas' => $item->nama_fakultas,
            ];
        })->toArray();
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
            $this->jurusan_kode = $data->kode ?? 'UNI';
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
