<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

use App\Models\ProgramStudi\Prodi;

class Dosen extends Model
{
    use HasFactory;
    protected $table = 'dosens';

    protected $fillable = [
        'user_id',
        'prodi_id',
        'name',
        'nip',
        'nidn',
        'nidk',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class)->withTrashed();
    }

    protected static function booted()
    {
        static::saving(function ($dosen) {
            if ($dosen->nip) {
                $exists = DB::table('admins')
                    ->where('nip', $dosen->nip)
                    ->exists();

                if ($exists) {
                    throw new \Exception("NIP {$dosen->nip} sudah digunakan oleh Admin!");
                }
            }
        });
    }
}