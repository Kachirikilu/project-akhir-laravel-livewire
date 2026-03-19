<?php

namespace App\Livewire\Admin\MatkulManagement;

use App\Models\MataKuliah;
use Livewire\WithPagination;

trait WithMataKuliahFilters
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public $sortField = 'nama_matkul';

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

                // 2. Cari berdasarkan ID atau Semester jika numerik
                if (is_numeric($search)) {
                    $q->orWhere('mata_kuliahs.id', $search)
                        ->orWhere('semester', $search);
                }

                // 3. Cari berdasarkan "Wajib" atau "Pilihan"
                if (strtolower($search) === 'wajib') {
                    $q->orWhere('is_wajib', 1);
                } elseif (strtolower($search) === 'pilihan') {
                    $q->orWhere('is_wajib', 0);
                }

                // 4. Cari berdasarkan Tipe SKS (Tatap Muka, TM, Praktikum, dll)
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
                // dd($searchLower, $tipeMap[$searchLower]);
     
                // 5. Cari berdasarkan Kode Lengkap atau Terpenggal (Partial Code Search)
                // Contoh: 'UNI10', 'TKE', '1102', 'EK11'
                $cleanSearch = strtoupper($search);

                // Kita cek apakah input mengandung unsur huruf (prefix) atau angka (digit)
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
                                        ->when($prefixPart === 'UNI', fn ($uni) => $uni->orWhere('tingkatan_mk', '4'));
                                });
                            }

                            // Jika ada bagian ANGKA (misal: "10" atau "1102")
                            if (! empty($digitPart)) {
                                // Jika user mengetik 1-2 digit, cari di digit_semester (tahun/ganjil-genap)
                                if (strlen($digitPart) <= 2) {
                                    $sub->where('digit_semester', 'like', $digitPart.'%');
                                }
                                // Jika user mengetik lebih dari 2 digit, pecah ke semester dan urutan MK
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
        if ($this->sortField === 'nama_matkul') {
            $query->orderBy('nama_matkul', $this->sortDirection);
        } elseif ($this->sortField === 'semester') {
            $query->orderBy('semester', $this->sortDirection);
        } elseif ($this->sortField === 'sks') {
            $query->orderBy('sks_kuliah', $this->sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }
    }
}
