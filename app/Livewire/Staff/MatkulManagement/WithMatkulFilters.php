<?php

namespace App\Livewire\Staff\MatkulManagement;


use App\Models\MataKuliah;
use Livewire\WithPagination;

trait WithMatkulFilters
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inputMainSearch()
    {
        $query = MataKuliah::query()->with(['prodis.jurusan_rel.fakultas_rel']);
        $search = trim($this->search);
        $searchTerm = '%'.$search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($search, $searchTerm) {
                // 1. Cari Nama & Kode Manual
                $q->where('nama_matkul', 'like', $searchTerm)
                    ->orWhere('kode_mk', 'like', $searchTerm);

                // 1. Cari Semester
                $cleanSearch = $search;
                if (preg_match('/(?:s|sem|semester)\s*(\d+)/i', $search, $matches)) {
                    $cleanSearch = $matches[1];
                }

                if (is_numeric($cleanSearch)) {
                    $q->orWhere(function($sub) use ($cleanSearch, $search) {
                        $sub->where('mata_kuliahs.id', $search) 
                            ->orWhere('semester', $cleanSearch);
                    });
                }

                // 3. Cari berdasarkan "Wajib" atau "Pilihan"
                if (strtolower($search) === 'wajib') {
                    $q->orWhere('is_wajib', 1);
                } elseif (strtolower($search) === 'pilihan') {
                    $q->orWhere('is_wajib', 0);
                }

                // 4. Cari berdasarkan "Digit MK" (No Urut 01, 02, dst)
                if (preg_match('/^\d+$/', $search)) { 
                    $q->orWhere('digit_mk', $search);
                } else {
                    $q->orWhere('digit_mk', 'LIKE', '%' . $search . '%');
                }

                // 5. Cari berdasarkan Tipe SKS (Tatap Muka, TM, Praktikum, dll)
                $tipeMap = [
                    'tm' => 1, 'tatap muka' => 1, 'teori' => 1,
                    'pr' => 2, 'praktikum' => 2, 'praktek' => 2,
                    'pl' => 3, 'praktek lapangan' => 3, 'lapangan' => 3,
                    'sm' => 4, 'simulasi' => 4, 'studio' => 4,
                ];
                $searchLower = strtolower($search);

                if (array_key_exists($searchLower, $tipeMap)) {
                    $q->orWhere('tipe_sks', $tipeMap[$searchLower]);
                }

                // 6. Cari berdasarkan Kode Lengkap atau Terpenggal (Partial Code Search)
                $cleanSearch = strtoupper($search);

                if (preg_match('/[A-Z]/', $cleanSearch) || preg_match('/[0-9]/', $cleanSearch)) {
                    $q->orWhere(function ($sq) use ($cleanSearch) {
                        // Pisahkan Huruf dan Angka dari input user
                        $prefixPart = preg_replace('/[^A-Z]/', '', $cleanSearch); // Ambil huruf saja
                        $digitPart = preg_replace('/[^0-9]/', '', $cleanSearch);  // Ambil angka saja

                        $sq->where(function ($sub) use ($prefixPart, $digitPart) {
                            // Jika ada bagian HURUF (misal: "TEK")
                            if (! empty($prefixPart)) {
                                $sub->where(function ($low) use ($prefixPart) {
                                    $low->where('kode_mk', 'like', $prefixPart.'%')
                                        ->orWhereHas('prodis', function ($pro) use ($prefixPart) {
                                            $pro->where('kode_pr', 'like', $prefixPart.'%')
                                                ->orWhereHas('jurusan_rel', function ($jur) use ($prefixPart) {
                                                    $jur->where('kode_jr', 'like', $prefixPart.'%')
                                                        ->orWhereHas('fakultas_rel', function ($fak) use ($prefixPart) {
                                                            $fak->where('kode_fk', 'like', $prefixPart.'%');
                                                        });
                                                });
                                        })
                                        ->when($prefixPart === 'UNI', fn ($uni) => $uni->orWhere('tingkatan_mk', '4'))
                                        ->when($prefixPart === 'UNI', fn ($uni) => $uni->orWhere('tingkatan_mk', '3'))
                                        ->when($prefixPart === 'UNI', fn ($uni) => $uni->orWhere('tingkatan_mk', '2'))
                                        ->when($prefixPart === 'UNI', fn ($uni) => $uni->orWhere('tingkatan_mk', '1'));
                                });
                            }

                            // Jika ada bagian ANGKA (misal: "10" atau "1102")
                            if (! empty($digitPart)) {
                                if (strlen($digitPart) <= 2) {
                                    $sub->where('digit_semester', 'like', $digitPart.'%');
                                }
                                else {
                                    $dSem = substr($digitPart, 0, 2);
                                    $dMk = substr($digitPart, 2);
                                    $sub->where('digit_semester', 'like', $dSem.'%')
                                        ->where('digit_mk', 'like', $dMk.'%');
                                }
                            }
                        });
                    });
                }

                // 6. Cari berdasarkan silsilah (Prodi/Jurusan/Fakultas)
                $q->orWhereHas('prodis', function ($pq) use ($searchTerm) {
                    $pq->where('nama_prodi', 'like', $searchTerm)
                        ->orWhere('kode_pr', 'like', $searchTerm)
                        ->orWhereHas('jurusan_rel', function ($jq) use ($searchTerm) {
                            $jq->where('nama_jurusan', 'like', $searchTerm)
                                ->orWhere('kode_jr', 'like', $searchTerm)
                                ->orWhereHas('fakultas_rel', function ($fq) use ($searchTerm) {
                                    $fq->where('nama_fakultas', 'like', $searchTerm)
                                        ->orWhere('kode_fk', 'like', $searchTerm);
                                });
                        });
                });
            });
        }

        // Filter Dropdown Silsilah (Tetap di luar closure search)
        if (! empty($this->selectedProdiId)) {
            $query->whereHas('prodis', fn ($q) => $q->where('prodis.id', $this->selectedProdiId));
        }
        if (! empty($this->selectedJurusanId)) {
            $query->whereHas('prodis', fn ($q) => $q->where('jurusan_id', $this->selectedJurusanId));
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('prodis.jurusan_rel', fn ($q) => $q->where('fakultas_id', $this->selectedFakultasId));
        }

        // Filter Tab/Pills
        // if (! empty($this->filter)) {
        //     if (is_numeric($this->filter)) {
        //         $query->where('semester', $this->filter);
        //     }
        // }

        $this->sortFieldOrder($query);

        return $query;
    }

    public function filterBy($mk)
    {
        $this->filter = $mk;
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filter']);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function sortFieldOrder($query)
    {
        $query->select('mata_kuliahs.*');

        if ($this->sortField === 'matkul') {
            $query->orderBy('nama_matkul', $this->sortDirection);
        } elseif ($this->sortField === 'semester') {
            $query->orderBy('semester', $this->sortDirection);
        } elseif ($this->sortField === 'sks') {
            $query->orderBy('sks_kuliah', $this->sortDirection);
        } elseif ($this->sortField === 'wajib') {
            $query->orderBy('is_wajib', $this->sortDirection);
        } elseif ($this->sortField === 'digit_mk') {
            $query->orderBy('digit_mk', $this->sortDirection);
        } elseif (in_array($this->sortField, ['sks_tm', 'sks_pr', 'sks_pl', 'sks_sm'])) {
            $typeMap = ['sks_tm' => 1, 'sks_pr' => 2, 'sks_pl' => 3, 'sks_sm' => 4];
            $targetType = $typeMap[$this->sortField];

            $query->orderByRaw("CASE WHEN tipe_sks = $targetType THEN sks_kuliah ELSE 0 END $this->sortDirection");
        } elseif ($this->sortField === 'kode') {
            $query->leftJoin('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
                ->leftJoin('prodis', 'prodi_pivot_mk.prodi_id', '=', 'prodis.id')
                ->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id');
            $prefixSql = "MAX(CASE 
        WHEN mata_kuliahs.tingkatan_mk = 1 THEN UPPER(mata_kuliahs.kode_mk)
        WHEN mata_kuliahs.tingkatan_mk = 2 THEN COALESCE(prodis.kode_pr, jurusans.kode_jr, fakultas.kode_fk, 'UNI')
        WHEN mata_kuliahs.tingkatan_mk = 3 THEN COALESCE(jurusans.kode_jr, fakultas.kode_fk, 'UNI')
        WHEN mata_kuliahs.tingkatan_mk = 4 THEN COALESCE(fakultas.kode_fk, 'UNI')
        ELSE 'UNI'
    END)";
            $query->groupBy('mata_kuliahs.id');
            $query->orderByRaw("
        CONCAT(
            $prefixSql, 
            MAX(mata_kuliahs.digit_semester), 
            MAX(mata_kuliahs.digit_mk)
        ) $this->sortDirection
    ");
        } else {
            $query->orderBy('mata_kuliahs.id', 'desc');
        }

        return $query;
    }
}
