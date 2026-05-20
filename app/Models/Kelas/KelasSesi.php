<?php

namespace App\Models\Kelas;

use App\Models\Auth\Dosen;
use App\Models\Akademik\Referensi;
use App\Models\Kelas\MahasiswaKehadiran;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

        if (!$rps) {
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

            if ($p == 8) {
                return (object) ['metode' => 'UTS', 'deskripsi' => 'Ujian Tengah Semester'];
            }
            if ($p == 16) {
                return (object) ['metode' => 'UAS', 'deskripsi' => 'Ujian Akhir Semester'];
            }

            $targetIndex = ($p < 8) ? ($p - 1) : ($p - 2);
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
}