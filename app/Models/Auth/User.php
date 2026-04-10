<?php

namespace App\Models\Auth;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // 'name',
        'email',
        'password',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'name',
        'role',
        'identity1',
        'identity2',
        'identity3',
        'status',
    ];
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeInLocationUser($query, $type, $id)
    {
        if (!$id) return $query;

        return $query->where(function ($q) use ($type, $id) {
            $roles = ['admin', 'dosen', 'mahasiswa'];
            foreach ($roles as $role) {
                $q->orWhereHas($role . ($type !== 'prodi' ? '.pr_rel' : ''), function ($r) use ($type, $id) {
                    if ($type === 'prodi') $r->where('pr_id', $id);
                    if ($type === 'jurusan') $r->where('jr_id', $id);
                    if ($type === 'fakultas') {
                        $r->whereHas('jr_rel', fn($j) => $j->where('fk_id', $id));
                    }
                });
            }
        });
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }


    /// Relasi User ke Admin/Dosen/Mahasiswa /// Relasi User ke Admin/Dosen/Mahasiswa /// Relasi User ke Admin/Dosen/Mahasiswa
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }
    public function dosen(): HasOne
    {
        return $this->hasOne(Dosen::class);
    }
    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }
    /// Relasi User ke Admin/Dosen/Mahasiswa /// Relasi User ke Admin/Dosen/Mahasiswa /// Relasi User ke Admin/Dosen/Mahasiswa

    /// ... /// ... /// ...

    /// Attribute Utama User /// Attribute Utama User /// Attribute Utama User 
    protected function name(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);
            return $profile?->name ?? $this->email;
        });
    }
    public function role(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->admin()->exists()     => 'Admin',
            $this->dosen()->exists()     => 'Dosen',
            $this->mahasiswa()->exists() => 'Mahasiswa',
            default                      => 'User',
        });
    }
    protected function identity1(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->nip;
            } elseif ($this->dosen) {
                $value = $this->dosen->nip;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->nim;
            }

            return empty($value) ? null : $value;
        });
    }
    protected function identity2(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->nitk;
            } elseif ($this->dosen) {
                $value = $this->dosen->nidn;
            }

            return empty($value) ? null : $value;
        });
    }
    protected function identity3(): Attribute
    {
        return Attribute::get(function () {
            $value = $this->dosen?->nidk;

            return $value ?: null;
        });
    }
    protected function status(): Attribute
    {
        return Attribute::get(fn () => 
            $this->admin?->status ?? 
            $this->dosen?->status ?? 
            $this->mahasiswa?->status ?? 
            'Tidak Ada'
        );
    }
    /// Attribute Utama User /// Attribute Utama User /// Attribute Utama User 

    /// ... /// ... /// ...

    /// Attribute Prodi/Jurusan/Fakultas /// Attribute Prodi/Jurusan/Fakultas /// Attribute Prodi/Jurusan/Fakultas 
    protected function prId(): Attribute
    {
        return Attribute::get(function () {
            return $this->admin?->pr_id 
                ?? $this->dosen?->pr_id 
                ?? $this->mahasiswa?->pr_id;
        });
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel->prodi;
        });
    }

    protected function kodePr(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel->kode;
        });
    }

    protected function jrId(): Attribute
    {
        return Attribute::get(function () {
            $prodi = $this->admin?->pr_rel ?? $this->dosen?->pr_rel ?? $this->mahasiswa?->pr_rel;
            return $prodi?->jr_id;
        });
    }

    protected function fkId(): Attribute
    {
        return Attribute::get(function () {
            $prodi = $this->admin?->pr_rel ?? $this->dosen?->pr_rel ?? $this->mahasiswa?->pr_rel;
            return $prodi?->jr_rel?->fk_id;
        });
    }
    /// Attribute Prodi/Jurusan/Fakultas /// Attribute Prodi/Jurusan/Fakultas /// Attribute Prodi/Jurusan/Fakultas 

    /// ... /// ... /// ...

    /// Attribute Pendamping /// Attribute Pendamping /// Attribute Pendamping
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->profile_photo_path) {
                return Storage::disk('public')->url($this->profile_photo_path);
            }
            return $this->defaultProfilePhotoUrl();
        });
    }
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(fn ($segment) => mb_substr($segment, 0, 1))->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=0080FF';
    }
    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
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
    /// Attribute Pendamping /// Attribute Pendamping /// Attribute Pendamping

    /// ... /// ... /// ...


    /// Search User /// Search User /// Search User
    public function scopeSearchUser($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
            // 1. Search di Tabel Users Utama
            $q->where('email', 'like', $searchTerm);

            if (is_numeric($search)) {
                $q->orWhere('users.id', $search);
            }

                 $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(users.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("DATE_FORMAT(users.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(users.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
                });

            // 2. Definisi Role dan Field Spesifiknya
            $roleConfigs = [
                'admin' => ['name', 'nip', 'nitk', 'status'],
                'dosen' => ['name', 'nip', 'nidn', 'nidk', 'status'],
                'mahasiswa' => ['name', 'nim', 'angkatan', 'status'],
            ];

            foreach ($roleConfigs as $role => $fields) {
                // Pencarian berdasarkan identitas role (NIP, Nama, dll)
                $q->orWhereHas($role, function ($r) use ($searchTerm, $fields) {
                    $r->where(function ($sub) use ($searchTerm, $fields) {
                        foreach ($fields as $field) {
                            $sub->orWhere($field, 'like', $searchTerm);
                        }
                    });
                });

                // Pencarian berdasarkan lokasi (Prodi, Jurusan, Fakultas)
                $q->orWhereHas("$role.pr_rel", function ($p) use ($searchTerm) {
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

                // Pencarian Berdasarkan Nama Role Langsung (ketik 'admin', 'dosen', dsb)
                if (str_contains($searchLower, $role)) {
                    $q->orWhereHas($role);
                }
            }
        });
    }
    /// Search User /// Search User /// Search User
}
