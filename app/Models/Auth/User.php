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
use Carbon\Carbon;

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
        'identity1',
        'identity2',
        'identity3',
        'role',
        'status',
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
                $q->orWhereHas($role . ($type !== 'prodi' ? '.prodi' : ''), function ($r) use ($type, $id) {
                    if ($type === 'prodi') $r->where('prodi_id', $id);
                    if ($type === 'jurusan') $r->where('jurusan_id', $id);
                    if ($type === 'fakultas') {
                        $r->whereHas('jurusan_rel', fn($j) => $j->where('fakultas_id', $id));
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

    protected function name(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->name ?? $this->email;
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

    public function role(): Attribute
    {
        return Attribute::get(function () {

            if ($this->admin) {
                return 'Admin';
            }
            if ($this->dosen) {
                return 'Dosen';
            }
            if ($this->mahasiswa) {
                return 'Mahasiswa';
            }

            return 'User';

        });
    }

    protected function status(): Attribute
    {
        return Attribute::get(function () {

            if ($this->admin) {
                return $this->admin->status;
            }
            if ($this->dosen) {
                return $this->dosen->status;
            }
            if ($this->mahasiswa) {
                return $this->mahasiswa->status;
            }

            return 'Tidak Ada';

        });
    }

    protected function createdDay(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->created_at) {
                return null;
            }

            return Carbon::parse($this->created_at)->translatedFormat('D, d M Y');
        });
    }
    protected function updatedDay(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->updated_at) {
                return null;
            }

            return Carbon::parse($this->updated_at)->translatedFormat('D, d M Y');
        });
    }

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

    protected function prodiId(): Attribute
    {
        return Attribute::get(function () {
            return $this->admin?->prodi_id 
                ?? $this->dosen?->prodi_id 
                ?? $this->mahasiswa?->prodi_id;
        });
    }

    protected function jurusanId(): Attribute
    {
        return Attribute::get(function () {
            $prodi = $this->admin?->prodi ?? $this->dosen?->prodi ?? $this->mahasiswa?->prodi;
            return $prodi?->jurusan_id;
        });
    }

    protected function fakultasId(): Attribute
    {
        return Attribute::get(function () {
            $prodi = $this->admin?->prodi ?? $this->dosen?->prodi ?? $this->mahasiswa?->prodi;
            return $prodi?->jurusan_rel?->fakultas_id;
        });
    }

    /**
     * Dapatkan URL foto profil pengguna.
     * * Accessor akan membuat properti profile_photo_url
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->profile_photo_path) {
                return Storage::disk('public')->url($this->profile_photo_path);
            }

            return $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * Dapatkan URL default foto profil (misalnya, Gravatar atau placeholder).
     * * Anda dapat menyesuaikan fungsi ini sesuai kebutuhan
     */
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
                'mahasiswa' => ['name', 'nim', 'tahun_angkatan', 'status'],
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
                $q->orWhereHas("$role.prodi", function ($p) use ($searchTerm) {
                    $p->where('nama_prodi', 'like', $searchTerm)
                        ->orWhereHas('jurusan_rel', function ($j) use ($searchTerm) {
                            $j->where('nama_jurusan', 'like', $searchTerm)
                                ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])
                                ->orWhereHas('fakultas_rel', function ($f) use ($searchTerm) {
                                    $f->where('nama_fakultas', 'like', $searchTerm)
                                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
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
}
