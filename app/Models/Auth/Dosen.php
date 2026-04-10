<?php

namespace App\Models\Auth;

use App\Models\ProgramStudi\Prodi;
use App\Models\Akademik\RPS;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosens';

    protected $fillable = [
        'user_id',
        'pr_id',
        'name',
        'nip',
        'nidn',
        'nidk',
        'status',
    ];

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(RPS::class, 'rps_pivot_dosen', 'rps_id', 'dosen_id')
                    ->withPivot(['peran', 'is_ketua', 'sort_order'])
                    ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'pr_id');
    }

    protected function nidnNidk(): Attribute
    {
        return Attribute::get(function () {
            $nidn = $this->nidn ?? '---';
            $nidk = $this->nidk ?? '---';
            return "NIDN: {$nidn} / NIDK: {$nidk}";
        });
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

    public function scopeSearchDosen($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
            // 1. Pencarian Identitas Langsung di Tabel Dosens
            $fields = ['name', 'nip', 'nidn', 'nidk', 'status'];
            foreach ($fields as $field) {
                $q->orWhere("dosens.$field", 'like', $searchTerm);
            }

            if (is_numeric($search)) {
                $q->orWhere('dosens.id', $search);
            }

            // 2. Pencarian ke Tabel User Terkait (Email & Timestamps)
            $q->orWhereHas('user', function ($u) use ($searchTerm, $searchLower) {
                $u->where('email', 'like', $searchTerm)
                    ->orWhere(function ($dq) use ($searchTerm, $searchLower) {
                        $dq->whereRaw("DATE_FORMAT(users.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                            ->orWhereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                            ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%a, %d %b %Y')) LIKE ?", [$searchLower])
                            ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%W, %d %M %Y')) LIKE ?", [$searchLower])
                            ->orWhereRaw("DATE_FORMAT(users.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                            ->orWhereRaw("DATE_FORMAT(users.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                            ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%a, %d %b %Y')) LIKE ?", [$searchLower])
                            ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%W, %d %M %Y')) LIKE ?", [$searchLower]);
                    });
            });

            // 3. Pencarian Berdasarkan Lokasi (Prodi, Jurusan, Fakultas)
            $q->orWhereHas('prodi', function ($p) use ($searchTerm) {
                $p->where('nama_pr', 'like', $searchTerm)
                    ->orWhereHas('jr_rel', function ($j) use ($searchTerm) {
                        $j->where('nama_jr', 'like', $searchTerm)
                            ->orWhereRaw("CONCAT('Jurusan ', nama_jr) LIKE ?", [$searchTerm])
                            ->orWhereHas('fk_rel', function ($f) use ($searchTerm) {
                                $f->where('nama_fk', 'like', $searchTerm)
                                    ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);
                            });
                    });
            });
        });
    }
}
