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
        'nim',
        'user_id',
        'pr_id',
        'name',
        'angkatan',
        'yudisium',
        'wisuda',
        'status',
    ];

    protected $casts = [
        'yudisium' => 'date',
        'wisuda' => 'date',
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