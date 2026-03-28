<?php

namespace App\Models\Akademik;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataKuliah extends Model
{
    use SoftDeletes;

    protected $table = 'mata_kuliahs';

    protected $fillable = [
        'tingkatan_mk', 'kode_mk', 'digit_semester', 'digit_mk',
        'nama_matkul', 'semester', 'sks_kuliah', 'tipe_sks',
        'is_wajib', 'bahan_kajian', 'deskripsi',
    ];

    protected $appends = ['kode', 'kode_blok', 'matkul', 'sks_tm', 'sks_pr', 'sks_pl', 'sks_sm', 'tipe_sks_text'];

    public function prodis()
    {
        return $this->belongsToMany(Prodi::class, 'prodi_pivot_mk', 'mk_id', 'prodi_id')
            ->withTrashed()
            ->withPivot('sort_order')
            ->orderBy('prodi_pivot_mk.sort_order', 'asc');
    }

    // protected function tingkatanMode(): Attribute
    // {
    //     return Attribute::get(function () {
    //         return match ((int) $this->tingkatan_mk) {
    //             1 => 'mk-prodi',
    //             2 => 'mk-jurusan',
    //             3 => 'mk-fakultas',
    //             4 => 'mk-universitas',
    //             default => 'mk',
    //         };
    //     });
    // }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $prefix = 'UNI';
            $prefixDefault = $this->kode_mk ? strtoupper($this->kode_mk) : null;
            $prodi = $this->prodis->first();

            if ($prodi) {
                if ($this->tingkatan_mk == 1) { // Tingkat Prodi
                    $prefix = $prodi->kode_pr ?? $prodi->jurusan_rel?->fakultas_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->tingkatan_mk == 2) { // Tingkat Jurusan
                    $prefix = $prodi->jurusan_rel?->kode_jr ?? $prodi->jurusan_rel?->fakultas_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->tingkatan_mk == 3) { // Tingkat Fakultas
                    $prefix = $prodi->jurusan_rel?->fakultas_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->tingkatan_mk == 4) { // Tingkat Universitas
                    $prefix = $prefixDefault ?? 'UNI';
                }
            } else {
                $prefix = $prefixDefault ?? 'UNI';
            }

            return $prefix.'-'.$this->digit_semester.$this->digit_mk;
        });
    }

    protected function kodeBlok(): Attribute
    {
        return Attribute::get(function () {
            $lastDigit = substr($this->digit_semester, -1);

            return match ($lastDigit) {
                '1', '2' => 1,
                '0' => 0,
                default => 1,
            };
        });
    }

    // Helper untuk mengambil objek prodi pertama (Eager Loaded)
    // protected function getFirstProdi()
    // {
    //     // Menggunakan relationLoaded untuk mencegah N+1 Query jika belum di-load
    //     return $this->prodis->first();
    // }

    // protected function prodiId(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->id);
    // }

    // protected function kodePr(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->kode_prodi);
    // }

    // protected function namaProdi(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->nama_prodi);
    // }

    // // Data Jurusan (Asumsi Prodi belongsTo Jurusan)
    // protected function jurusanId(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->jurusan?->id);
    // }

    // protected function kodeJr(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->jurusan?->kode_jurusan);
    // }

    // protected function namaJurusan(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->jurusan?->nama_jurusan);
    // }

    // // Data Fakultas (Asumsi Jurusan belongsTo Fakultas)
    // protected function fakultasId(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->jurusan?->fakultas?->id);
    // }

    // protected function kodeFk(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->jurusan?->fakultas?->kode_fakultas);
    // }

    // protected function namaFakultas(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->jurusan?->fakultas?->nama_fakultas);
    // }

    protected function matkul(): Attribute
    {
        return Attribute::get(fn () => $this->nama_matkul);
    }

    // protected function semesterText(): Attribute
    // {
    //     return Attribute::get(fn () => 'Semester ' . $this->semester);
    // }

    protected function sks(): Attribute
    {
        return Attribute::get(fn () => $this->sks_kuliah);
    }

    // 0: Tatap Muka (TM)
    protected function sksTm(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 1 ? $this->sks_kuliah : null);
    }

    // 1: Praktikum (PR)
    protected function sksPr(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 2 ? $this->sks_kuliah : null);
    }

    // 2: Praktek Lapangan (PL)
    protected function sksPl(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 3 ? $this->sks_kuliah : null);
    }

    // 3: Simulasi (SM)
    protected function sksSm(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 4 ? $this->sks_kuliah : null);
    }

    protected function tipeSksText(): Attribute
    {
        return Attribute::get(function () {
            return match ((int) $this->tipe_sks) {
                1 => 'Tatap Muka',
                2 => 'Praktikum',
                3 => 'Praktek Lapangan',
                4 => 'Simulasi',
                0 => 'Teori',
                default => 'Tidak Diketahui',
            };
        });
    }

    protected function wajib(): Attribute
    {
        return Attribute::get(fn () => $this->is_wajib);
    }

    protected function wajibText(): Attribute
    {
        return Attribute::get(fn () => $this->is_wajib == 1 ? 'Wajib' : 'Pilihan');
    }


    public function scopeSearchMK($query, $search)
    {
        $search = trim($search);
        $searchTerm = '%' . $search . '%';

        return $query->where(function ($q) use ($search, $searchTerm) {
            // 1. Cari Nama & Kode Manual
            $q->where('mata_kuliahs.nama_matkul', 'like', $searchTerm)
                ->orWhere('mata_kuliahs.kode_mk', 'like', $searchTerm);

            // 2. Cari Semester (dengan Regex)
            $cleanSearch = $search;
            if (preg_match('/(?:s|sem|semester)\s*(\d+)/i', $search, $matches)) {
                $cleanSearch = $matches[1];
            }

            if (is_numeric($cleanSearch)) {
                $q->orWhere(function ($sub) use ($cleanSearch, $search) {
                    $sub->where('mata_kuliahs.id', $search)
                        ->orWhere('mata_kuliahs.semester', $cleanSearch);
                });
            }

            // 3. Wajib atau Pilihan
            if (strtolower($search) === 'wajib') {
                $q->orWhere('mata_kuliahs.is_wajib', 1);
            } elseif (strtolower($search) === 'pilihan') {
                $q->orWhere('mata_kuliahs.is_wajib', 0);
            }

            // 4. Digit MK
            if (preg_match('/^\d+$/', $search)) {
                $q->orWhere('mata_kuliahs.digit_mk', $search);
            } else {
                $q->orWhere('mata_kuliahs.digit_mk', 'LIKE', $searchTerm);
            }

            // 5. Tipe SKS
            $tipeMap = [
                'tm' => 1, 'tatap muka' => 1, 'teori' => 1,
                'pr' => 2, 'praktikum' => 2, 'praktek' => 2,
                'pl' => 3, 'praktek lapangan' => 3, 'lapangan' => 3,
                'sm' => 4, 'simulasi' => 4, 'studio' => 4,
            ];
            $searchLower = strtolower($search);
            if (array_key_exists($searchLower, $tipeMap)) {
                $q->orWhere('mata_kuliahs.tipe_sks', $tipeMap[$searchLower]);
            }

            // 6. Partial Code Search (Prefix & Digits)
            $cleanSearchUpper = strtoupper($search);
            if (preg_match('/[A-Z0-9]/', $cleanSearchUpper)) {
                $q->orWhere(function ($sq) use ($cleanSearchUpper) {
                    $prefixPart = preg_replace('/[^A-Z]/', '', $cleanSearchUpper);
                    $digitPart = preg_replace('/[^0-9]/', '', $cleanSearchUpper);

                    $sq->where(function ($sub) use ($prefixPart, $digitPart) {
                        if (!empty($prefixPart)) {
                            $sub->where(function ($low) use ($prefixPart) {
                                $low->where('mata_kuliahs.kode_mk', 'like', $prefixPart . '%')
                                    ->orWhereHas('prodis', function ($pro) use ($prefixPart) {
                                        $pro->where('kode_pr', 'like', $prefixPart . '%')
                                            ->orWhereHas('jurusan_rel', function ($jur) use ($prefixPart) {
                                                $jur->where('kode_jr', 'like', $prefixPart . '%')
                                                    ->orWhereHas('fakultas_rel', function ($fak) use ($prefixPart) {
                                                        $fak->where('kode_fk', 'like', $prefixPart . '%');
                                                    });
                                            });
                                    })
                                    ->when($prefixPart === 'UNI', fn($uni) => $uni->orWhere('tingkatan_mk', '4'));
                            });
                        }

                        if (!empty($digitPart)) {
                            if (strlen($digitPart) <= 2) {
                                $sub->where('mata_kuliahs.digit_semester', 'like', $digitPart . '%');
                            } else {
                                $dSem = substr($digitPart, 0, 2);
                                $dMk = substr($digitPart, 2);
                                $sub->where('mata_kuliahs.digit_semester', 'like', $dSem . '%')
                                    ->where('mata_kuliahs.digit_mk', 'like', $dMk . '%');
                            }
                        }
                    });
                });
            }

            // 7. Silsilah (Prodi/Jurusan/Fakultas)
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
}
