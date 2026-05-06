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

class SesiKelas extends Model
{
    use SoftDeletes;

    protected $table = 'sesi_kelas';
    protected $guarded = ['id'];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(KelasJadwal::class, 'kj_id');
    }

    public function override(): HasOne
    {
        return $this->hasOne(SesiKelasOverride::class, 'sesi_id');
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
}