<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

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
     * * @var array
     */
    protected $appends = [
        'profile_photo_url', 
        'name',
        'identity',
        'identity2',
        'identity3',
        'role',
        'status'
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

    // protected function identity(): Attribute
    // {
    //     return Attribute::get(function () {
    //         if ($this->admin) return $this->admin->nip;
    //         if ($this->dosen) return $this->dosen->nip;
    //         if ($this->mahasiswa) return $this->mahasiswa->nim;
    //         return null;
    //     });
    // }
    protected function identity(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) $value = $this->admin->nip;
            elseif ($this->dosen) $value = $this->dosen->nip;
            elseif ($this->mahasiswa) $value = $this->mahasiswa->nim;
            return empty($value) ? null : $value;
        });
    }
    protected function identity2(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) $value = $this->admin->nitk;
            elseif ($this->dosen) $value = $this->dosen->nidn;
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
    public function getRoleAttribute(): string
    {
        if ($this->admin) return 'Admin';
        if ($this->dosen) return 'Dosen';
        if ($this->mahasiswa) return 'Mahasiswa';
        return 'User';
    }
    protected function status(): Attribute
    {
        return Attribute::get(function () {

        if ($this->admin) return $this->admin->status;
        if ($this->dosen) return $this->dosen->status;
        if ($this->mahasiswa) return $this->mahasiswa->status;
        return 'Tidak Ada';
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

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=0080FF';
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
        });
    }
}
