<?php

namespace App\Models;

use App\Traits\ValidatesGlobalIdentity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;
    protected $table = 'admins';

    protected $fillable = [
        'user_id',
        'prodi_id',
        'nip',
        'nitk',
        'name',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    protected static function booted()
    {
        static::saving(function ($admin) {
            if ($admin->nip && \DB::table('dosens')->where('nip', $admin->nip)->exists()) {
                throw new \Exception("NIP ini sudah terdaftar sebagai Dosen.");
            }
        });
    }
}