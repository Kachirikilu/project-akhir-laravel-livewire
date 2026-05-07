<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikans';

    protected $fillable = [
        'user_id',
        'institusi',
        'negara',
        'tahun_lulus',
        'jenjang_pendidikan',
        'bidang_ilmu',
        'gelar',
        'is_pendidikan_blu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
