<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahasiswa extends Model
{
    use HasFactory;
    protected $table = 'mahasiswas';

    protected $fillable = [
        'nim',
        'user_id',
        'prodi_id',
        'name',
        'tahun_angkatan',
        'tanggal_yudisium',
        'tanggal_wisuda',
        'status',
    ];

    protected $casts = [
        'tanggal_yudisium' => 'date',
        'tanggal_wisuda' => 'date',
        'tahun_angkatan' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}