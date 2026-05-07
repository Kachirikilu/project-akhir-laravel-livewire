<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\ProgramStudi\Prodi;

class Mahasiswa extends Model
{
    use HasFactory;
    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'pr_id',
        'kode_wilayah',
        'name',
        'nim',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'no_hp',
        'angkatan',
        'tanggal_yudisium',
        'tanggal_wisuda',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_yudisium' => 'date',
        'tanggal_wisuda' => 'date',
        'angkatan' => 'integer',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'pr_id')->withTrashed();
    }
}