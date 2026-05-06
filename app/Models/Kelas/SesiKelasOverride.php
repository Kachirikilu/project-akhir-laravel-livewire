<?php

namespace App\Models\Kelas;

use App\Models\Auth\Dosen;
use App\Models\Kelas\SesiKelas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesiKelasOverride extends Model
{
    use SoftDeletes;

    protected $table = 'sesi_kelas_overrides';
    protected $guarded = ['id'];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SesiKelas::class, 'sesi_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}