<?php

namespace App\Models\Kelas;

use App\Models\Akademik\Referensi;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\Dosen;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KelasSesi extends Model
{
    use SoftDeletes;

    protected $table = 'kelas_sesi';

    protected $guarded = ['id'];

    public function jadwal_rel(): BelongsTo
    {
        return $this->belongsTo(KelasJadwal::class, 'kj_id');
    }

    public function override(): HasOne
    {
        return $this->hasOne(KelasSesiOverride::class, 'sesi_id');
    }

    public function getAllScpmkAttribute()
    {
        $rps = $this->jadwal_rel?->kelas_rel?->rps_rel;

        if (! $rps) {
            return collect();
        }

        return $rps->cpmks->flatMap(function ($cpmk) {
            return $cpmk->scpmks;
        })->values();
    }

    protected function subCpmk(): Attribute
    {
        return Attribute::get(function () {
            $p = $this->pertemuan_ke;
            $allScpmk = $this->all_scpmk;

            $hasUtsInRps = $allScpmk->contains(function ($item) {
                return Str::contains($item->deskripsi ?? '', SubCPMK::UTS_FIELDS, ignoreCase: true) ||
                       Str::contains($item->metode ?? '', SubCPMK::UTS_FIELDS, ignoreCase: true);
            });

            $hasUasInRps = $allScpmk->contains(function ($item) {
                return Str::contains($item->deskripsi ?? '', SubCPMK::UAS_FIELDS, ignoreCase: true) ||
                       Str::contains($item->metode ?? '', SubCPMK::UAS_FIELDS, ignoreCase: true);
            });

            if ($hasUtsInRps && $hasUasInRps) {
                $targetIndex = $p - 1;
            } elseif ($hasUtsInRps && ! $hasUasInRps) {
                if ($p == 16) {
                    return (object) ['metode' => 'UAS', 'deskripsi' => 'Ujian Akhir Semester'];
                }
                $targetIndex = $p - 1;
            } elseif (! $hasUtsInRps && $hasUasInRps) {
                if ($p == 8) {
                    return (object) ['metode' => 'UTS', 'deskripsi' => 'Ujian Tengah Semester'];
                }
                $targetIndex = ($p < 8) ? ($p - 1) : ($p - 2);
            } else {
                if ($p == 8) {
                    return (object) ['metode' => 'UTS', 'deskripsi' => 'Ujian Tengah Semester'];
                }
                if ($p == 16) {
                    return (object) ['metode' => 'UAS', 'deskripsi' => 'Ujian Akhir Semester'];
                }
                $targetIndex = ($p < 8) ? ($p - 1) : ($p - 2);
            }

            return $allScpmk->get($targetIndex) ?? (object) ['metode' => '-', 'deskripsi' => 'Materi belum ditentukan'];
        });
    }

    public function kehadirans(): HasMany
    {
        return $this->hasMany(MahasiswaKehadiran::class, 'sesi_id');
    }

    public function refs(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'sesi_pivot_ref', 'sesi_id', 'ref_id')
            ->withTimestamps();
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'sesi_pivot_dosen', 'sesi_id', 'dosen_id')
            ->withPivot(['peran', 'is_ketua', 'sort_order'])
            ->orderBy('sort_order')
            ->withTimestamps();
    }

    public function getPengajarAttribute()
    {
        if ($this->dosens()->exists()) {
            return $this->dosens;
        }

        return $this->jadwal->kelas_rel->rps_rel->dosens;
    }

    protected function hari(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->tanggal) {
                return '-';
            }

            return Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l');
        });
    }

    protected function metode(): Attribute
    {
        return Attribute::get(function () {
            return $this->override->metode ?? $this->sub_cpmk->metode ?? '-';
        });
    }

    protected function tanggalPelaksanaan(): Attribute
    {
        return Attribute::get(function () {
            return Carbon::parse($this->tanggal)->format('d/m/Y');
        });
    }

    protected function jamMulai(): Attribute
    {
        return Attribute::get(function () {
            return Carbon::parse($this->override->jam_mulai ?? $this->jadwal_rel->jam_mulai)->format('H:i');
        });
    }

    protected function jamBerakhir(): Attribute
    {
        return Attribute::get(function () {
            return Carbon::parse($this->override->jam_berakhir ?? $this->jadwal_rel->jam_berakhir)->format('H:i');
        });
    }

    protected function jamPelaksanaan(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->jam_mulai) {
                return '-';
            }

            return "{$this->jam_mulai} - {$this->jam_berakhir}";
        });
    }

    protected function createdDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->created_at) {
                return null;
            }

            return $this->created_at->translatedFormat('D, d M Y');
        });
    }

    protected function updatedDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->updated_at) {
                return null;
            }

            return $this->updated_at->translatedFormat('D, d M Y');
        });
    }

    public function scopeSearchKelasSesi($query, $search)
    {
        if (blank(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {

            // =========================================================================
            // 1. PENCARIAN NOMOR PERTEMUAN (P1, Per 1, Per1, Pertemuan 1, dsb)
            // =========================================================================
            if (preg_match('/(?:p|per|pertemuan)\s*(\d+)/i', $search, $matchPertemuan)) {
                $q->orWhere('kelas_sesi.pertemuan_ke', $matchPertemuan[1]);
            } elseif (is_numeric($search)) {
                $q->orWhere('kelas_sesi.pertemuan_ke', $search);
                $q->orWhere('kelas_sesi.id', $search);
            }

            // =========================================================================
            // 2. PENCARIAN TANGGAL & HARI PELAKSANAAN (Mendukung Date & Override)
            // =========================================================================
            // Cek nama hari pada tanggal sesi (0 = Senin, ..., 6 = Minggu)
            $q->orWhereRaw("CASE WEEKDAY(kelas_sesi.tanggal)
            WHEN 0 THEN 'senin' WHEN 1 THEN 'selasa' WHEN 2 THEN 'rabu'
            WHEN 3 THEN 'kamis' WHEN 4 THEN 'jumat' WHEN 5 THEN 'sabtu'
            WHEN 6 THEN 'minggu' END LIKE ?", [$searchLower]);

            // Cek format tanggal sesi (dd/mm/yyyy atau yyyy-mm-dd)
            $q->orWhereRaw("DATE_FORMAT(kelas_sesi.tanggal, '%d/%m/%Y') LIKE ?", [$searchTerm])
                ->orWhereRaw("DATE_FORMAT(kelas_sesi.tanggal, '%Y-%m-%d') LIKE ?", [$searchTerm]);

            // =========================================================================
            // 3. PENCARIAN JAM PELAKSANAAN (Mendukung Override & Jadwal Induk)
            // =========================================================================
            $q->orWhere(function ($jq) use ($searchTerm) {
                $jq->whereHas('override', function ($oq) use ($searchTerm) {
                    $oq->whereRaw("TIME_FORMAT(jam_mulai, '%H:%i') LIKE ?", [$searchTerm])
                        ->orWhereRaw("TIME_FORMAT(jam_berakhir, '%H:%i') LIKE ?", [$searchTerm])
                        ->orWhereRaw("CONCAT(TIME_FORMAT(jam_mulai, '%H:%i'), ' - ', TIME_FORMAT(jam_berakhir, '%H:%i')) LIKE ?", [$searchTerm]);
                })->orWhereHas('jadwal_rel', function ($gq) use ($searchTerm) {
                    // Fallback ke jadwal jika tidak dioverride
                    $gq->whereRaw("TIME_FORMAT(jam_mulai, '%H:%i') LIKE ?", [$searchTerm])
                        ->orWhereRaw("TIME_FORMAT(jam_berakhir, '%H:%i') LIKE ?", [$searchTerm])
                        ->orWhereRaw("CONCAT(TIME_FORMAT(jam_mulai, '%H:%i'), ' - ', TIME_FORMAT(jam_berakhir, '%H:%i')) LIKE ?", [$searchTerm]);
                });
            });

            // =========================================================================
            // 4. PENCARIAN JUMLAH MAHASISWA YANG HADIR
            // =========================================================================
            if (is_numeric($search)) {
                $q->orWhereHas('kehadirans', function ($kq) {
                    // Kosong karena kita hanya memicu agregat subquery count di bawah
                }, '=', (int) $search);
            }

            // =========================================================================
            // 5. PENCARIAN METODE PEMBELAJARAN (Melalui Relasi RPS / Sub-CPMK jika ada)
            // =========================================================================
            // $q->orWhereHas('jadwal_rel.kelas_rel.rps_rel.cpmks.scpmks', function ($sq) use ($searchTerm) {
            //     $sq->where('metode', 'LIKE', $searchTerm)
            //         ->orWhere('deskripsi', 'LIKE', $searchTerm);
            // });
            if (in_array(strtoupper($search), ['UTS', 'UJIAN', 'TENGAH'])) {
                $q->orWhere('kelas_sesi.pertemuan_ke', 8);
            }

            if (in_array(strtoupper($search), ['UAS', 'UJIAN', 'AKHIR'])) {
                $q->orWhere('kelas_sesi.pertemuan_ke', 16);
            }

            // =========================================================================
            // 6. PENCARIAN STRUKTUR KELAS (DARI JADWAL INDUK & KELAS RELASI)
            // =========================================================================
            $q->orWhereHas('jadwal_rel', function ($jq) use ($searchClean, $searchTerm) {
                $jq->where(function ($innerJq) use ($searchClean) {
                    preg_match('/^([A-Za-z]+\-?\d+)(?:\-?([A-Za-z]))?(?:\-?([A-Za-z]{0,3}))?(?:\-?(\d{0,4}))?$/i', $searchClean, $matches);
                    $kodeKelas = $matches[1] ?? null;
                    $label = $matches[2] ?? null;
                    $wilayah = $matches[3] ?? null;
                    $tahun = $matches[4] ?? null;

                    if ($kodeKelas) {
                        $kodeClean = preg_replace('/[^A-Za-z0-9]/', '', $kodeKelas);
                        $innerJq->whereHas('kelas_rel', function ($rq) use ($kodeClean, $searchClean) {
                            $rq->whereRaw("REPLACE(kode_kelas, '-', '') LIKE ?", ['%'.$kodeClean.'%'])
                                ->orWhere('kode_kelas', 'LIKE', $searchClean);
                        });
                    }
                    if ($label) {
                        $innerJq->where('label_kelas', 'LIKE', "%{$label}%");
                    }
                    if ($wilayah) {
                        $innerJq->where('kode_wilayah', 'LIKE', "%{$wilayah}%");
                    }
                    if ($tahun) {
                        if (strlen($tahun) >= 4) {
                            $tahun = substr($tahun, -2);
                        }
                        $innerJq->whereRaw('RIGHT(YEAR(tanggal_mulai), 2) LIKE ?', ["%{$tahun}%"]);
                    }
                    $innerJq->orWhereRaw("REPLACE(CONCAT(label_kelas, kode_wilayah, RIGHT(YEAR(tanggal_mulai), 2)), '-', '') LIKE ?", ['%'.$searchClean.'%']);
                })
                    ->orWhere('password', 'LIKE', $searchTerm)
                    ->orWhere('label_kelas', 'LIKE', $searchTerm)
                    ->orWhere('kode_wilayah', 'LIKE', $searchTerm)
                    ->orWhereRaw("CONCAT(label_kelas, ' ', kode_wilayah) LIKE ?", [$searchTerm]);
            });

            // =========================================================================
            // 7. DATA TIMESTAMPS RECORD SESI
            // =========================================================================
            $q->orWhereRaw("DATE_FORMAT(kelas_sesi.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                ->orWhereRaw("DATE_FORMAT(kelas_sesi.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                ->orWhereRaw("LOWER(DATE_FORMAT(kelas_sesi.created_at, '%a, %d %b %Y')) LIKE ?", [$searchLower])
                ->orWhereRaw("DATE_FORMAT(kelas_sesi.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                ->orWhereRaw("DATE_FORMAT(kelas_sesi.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm]);
        });
    }
}
